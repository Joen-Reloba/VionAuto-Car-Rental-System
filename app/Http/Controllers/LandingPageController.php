<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;

class LandingPageController extends Controller
{
    public function index()
    {
        // Fetch vehicles with their images
        $vehicles = Vehicle::with('images')
            ->where('status', 'available')
            ->limit(12)
            ->get()
            ->map(function ($vehicle) {
                return [
                    'vehicle_ID' => $vehicle->vehicle_ID,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'category' => $vehicle->category,
                    'daily_rate' => $vehicle->daily_rate,
                    'image' => $vehicle->images()
                        ->orderBy('is_primary', 'desc')
                        ->first()?->img_path ?? null,
                ];
            });

        // Get unique categories from all vehicles
        $categories = Vehicle::select('category')
            ->distinct()
            ->where('status', 'available')
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        // Get max price for range slider
        $maxPrice = Vehicle::where('status', 'available')
            ->max('daily_rate') ?? 10000;

        return view('landing_page', [
            'vehicles' => $vehicles,
            'categories' => $categories,
            'maxPrice' => $maxPrice,
        ]);
    }

    public function browseAllVehicles()
    {
        // Start with base query
        $query = Vehicle::with('images')
            ->where('status', 'available');

        // Get categories and brands for filters
        $categories = Vehicle::select('category')
            ->distinct()
            ->where('status', 'available')
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        $brands = Vehicle::select('brand')
            ->distinct()
            ->where('status', 'available')
            ->orderBy('brand')
            ->pluck('brand')
            ->toArray();

        // Apply filters if provided
        if (request('category') && request('category') !== '') {
            $query->where('category', request('category'));
        }

        if (request('brand') && request('brand') !== '') {
            $query->whereRaw('LOWER(brand) LIKE ?', ['%' . strtolower(request('brand')) . '%']);
        }

        if (request('price') && request('price') !== '') {
            [$minPrice, $maxPrice] = explode('-', request('price'));
            $minPrice = (float) $minPrice;
            $maxPrice = $maxPrice === '5000' ? 999999 : (float) $maxPrice;
            $query->whereBetween('daily_rate', [$minPrice, $maxPrice]);
        }

        // Fetch and format vehicles
        $vehicles = $query->get()
            ->map(function ($vehicle) {
                return [
                    'vehicle_ID' => $vehicle->vehicle_ID,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'category' => $vehicle->category,
                    'daily_rate' => $vehicle->daily_rate,
                    'image' => $vehicle->images()
                        ->orderBy('is_primary', 'desc')
                        ->first()?->img_path ?? null,
                ];
            });

        // Get max price for reference
        $maxPrice = Vehicle::where('status', 'available')
            ->max('daily_rate') ?? 10000;

        return view('customer.customer_browse_all_vehicles', [
            'vehicles' => $vehicles,
            'categories' => $categories,
            'brands' => $brands,
            'maxPrice' => $maxPrice,
            'selectedCategory' => request('category', ''),
            'selectedBrand' => request('brand', ''),
            'selectedPrice' => request('price', ''),
        ]);
    }

    public function viewVehicle($vehicleId)
    {
        // Fetch the vehicle with all its images
        $vehicle = Vehicle::with('images')
            ->where('vehicle_ID', $vehicleId)
            ->where('status', 'available')
            ->firstOrFail();

        // Format vehicle data
        $vehicleData = [
            'vehicle_ID' => $vehicle->vehicle_ID,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
            'category' => $vehicle->category,
            'color' => $vehicle->color,
            'daily_rate' => $vehicle->daily_rate,
             'description' => $vehicle->description,
            'images' => $vehicle->images->map(function ($img) {
                return [
                    'path' => $img->img_path,
                    'is_primary' => $img->is_primary,
                ];
            })->toArray(),
        ];

        return view('customer.customer_view_vehicle', [
            'vehicle' => $vehicleData,
        ]);
    }
}

