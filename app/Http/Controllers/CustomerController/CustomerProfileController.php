<?php

namespace App\Http\Controllers\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class CustomerProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

       $customer = Customer::with(['bookings.vehicle'])->where('user_ID', $user->user_ID)->firstOrFail();

        $totalBookings = $customer->bookings->count();
        $completed = $customer->bookings->whereIn('status', ['completed', 'finished'])->count();
        $ongoing= $customer->bookings->whereIn('status', ['active', 'ongoing'])->count();
         $totalSpent = $customer->bookings->whereIn('status', ['completed', 'finished'])->sum('total');

        return view('customer.customer_profile', compact(
            'user',
            'customer',
            'totalBookings',
            'completed',
            'ongoing',
            'totalSpent'
        ));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = \App\Models\User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->with('open_password_modal', true);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function update(Request $request)
{
    $user = Auth::user();
    $customer = Customer::where('user_ID', $user->user_ID)->firstOrFail();

    $request->validate([
        'first_name'      => 'required|string|max:255',
        'middle_name'     => 'nullable|string|max:255',
        'last_name'       => 'required|string|max:255',
        'phone_number'    => 'nullable|string|max:20',
        'birthday'        => 'nullable|date',
        'address'         => 'nullable|string|max:500',
        'license_no'      => 'nullable|string|max:50',
        'license_expiry'  => 'nullable|date',
        'valid_ID'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Update user table
    \App\Models\User::find($user->user_ID)->update([
        'first_name'   => $request->first_name,
        'middle_name'  => $request->middle_name,
        'last_name'    => $request->last_name,
        'phone_number' => $request->phone_number,
    ]);

        // Handle valid ID upload
    $validIdFilename = $customer->valid_ID;
    if ($request->hasFile('valid_ID')) {
        // Delete old file
        if ($customer->valid_ID) {
            Storage::disk('public')->delete('images-valid_id/' . $customer->valid_ID);
        }
        $file = $request->file('valid_ID');
        $validIdFilename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('images-valid_id', $validIdFilename, 'public');
    }

    // Update customer table
    $customer->update([
        'birthday'       => $request->birthday,
        'address'        => $request->address,
        'license_no'     => $request->license_no,
        'license_expiry' => $request->license_expiry,
        'valid_ID'       => $validIdFilename,
    ]);

    return back()->with('success', 'Profile updated successfully.');
}
}

