<?php

namespace App\Http\Controllers\StaffController;

use App\Models\Vehicle;
use App\Models\VehicleImg;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ManageVehicleController extends Controller
{
    public function create()
    {
        return view('staff.staff_add_vehicle');
    }

    public function index()
    {
        $cars = Vehicle::with('images')->get();
        
        // Format car data for JavaScript
        $carsData = $cars->map(function ($car) {
            $imagePath = null;
            
            // Try to get primary image first, then fallback to first image
            $vehicleImage = $car->images->where('is_primary', true)->first() ?? $car->images->first();
            
            if ($vehicleImage && $vehicleImage->img_path) {
                // Extract just the filename from the path and use .png extension
                $filename = pathinfo($vehicleImage->img_path, PATHINFO_FILENAME) . '.png';
                $imagePath = asset('assets/images/images-vehicles/' . $filename);
            }
            
            return [
                'id'         => $car->vehicle_ID,
                'brand'      => $car->brand,
                'model'      => $car->model,
                'year'       => $car->year ?? '—',
                'daily_rate' => $car->daily_rate,
                'status'     => $car->status,
                'type'       => $car->category,
                'color'      => $car->color,
                'plate_no'   => $car->plate_no,
                'created_at' => $car->created_at->format('m/d/Y'),
                'image'      => $imagePath,
                'image'      => $imagePath,
                'description' => $car->description, 
            ];
        })->toArray();
        
        return view('staff.staff_vehicles', compact('cars', 'carsData'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'color' => 'nullable|string|max:255',
                'plate_no' => 'required|string|max:20|unique:vehicles,plate_no',
                'category' => 'required|string|in:sedan,suv,van,pickup',
                'daily_rate' => 'required|numeric|min:0',
                'images' => 'required|array|min:1|max:3',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'nullable|string|max:1000',
            ]);

            $validated['status'] = 'available';

            $vehicle = Vehicle::create($validated);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $isPrimary = true;
                
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('assets/images/images-vehicles'), $filename);
                    
                    VehicleImg::create([
                        'vehicle_id' => $vehicle->vehicle_ID,
                        'img_path' => $filename,
                        'is_primary' => $isPrimary,
                    ]);
                    
                    $isPrimary = false;
                }
            }

            return redirect()->route('staff.vehicles')->with('success', 'Vehicle added successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput($request->except('images'));
        } catch (\Exception $e) {
            Log::error('Vehicle store error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error adding vehicle: ' . $e->getMessage())
                ->withInput($request->except('images'));
        }
    }

    public function edit(Vehicle $vehicle)
    {
        // Get all vehicle images from vehicle_imgs table (up to 3)
        $vehicleImages = [];
        $images = $vehicle->images()->orderBy('is_primary', 'desc')->get();
        
        foreach ($images as $image) {
            if ($image->img_path) {
                $vehicleImages[] = asset('assets/images/images-vehicles/' . $image->img_path);
            }
        }
        
        return view('staff.staff_update_vehicle', compact('vehicle', 'vehicleImages'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        try {
            $validated = $request->validate([
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'color' => 'nullable|string|max:255',
                'plate_no' => 'required|string|max:20|unique:vehicles,plate_no,' . $vehicle->vehicle_ID . ',vehicle_ID',
                'category' => 'required|string|in:sedan,suv,van,pickup',
                'daily_rate' => 'required|numeric|min:0',
                'status' => 'required|string|in:available,rented,maintenance,unavailable',
                'images' => 'nullable|array|max:3',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'nullable|string|max:1000',
            ]);

            // Handle image uploads (optional for update)
            if ($request->hasFile('images') && !empty($request->file('images'))) {
                // Delete old image files from disk
                foreach ($vehicle->images as $oldImage) {
                    $oldImagePath = public_path('assets/images/images-vehicles/' . $oldImage->img_path);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                // Delete old image records from database
                $vehicle->images()->delete();
                
                // Save new images
                $isPrimary = true;
                
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('assets/images/images-vehicles'), $filename);
                    
                    VehicleImg::create([
                        'vehicle_id' => $vehicle->vehicle_ID,
                        'img_path' => $filename,
                        'is_primary' => $isPrimary,
                    ]);
                    
                    $isPrimary = false;
                }
            }

            $vehicle->update($validated);

            return redirect()->route('staff.vehicles')->with('success', 'Vehicle updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput($request->except('images'));
        } catch (\Exception $e) {
            Log::error('Vehicle update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating vehicle: ' . $e->getMessage())
                ->withInput($request->except('images'));
        }
    }

    public function destroy(Vehicle $vehicle)
    {
        // Delete all image files from disk
        foreach ($vehicle->images as $image) {
            $imagePath = public_path('assets/images/images-vehicles/' . $image->img_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $vehicle->delete();
        
        return response()->json(['success' => true, 'message' => 'Vehicle deleted successfully']);
    }
}

