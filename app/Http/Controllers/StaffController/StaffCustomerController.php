<?php

namespace App\Http\Controllers\StaffController;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class StaffCustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->with('customer')
            ->paginate(10); // 10 customers per page

        // Format the paginated data for the view
        $customersData = $customers->getCollection()->map(function ($user) {
            return [
                'user_ID' => $user->user_ID,
                'name' => trim($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name),
                'phone_number' => $user->phone_number,
                'email' => $user->email,
                'birthday' => $user->customer?->birthday,
                'license_no' => $user->customer?->license_no,
                'license_expiry' => $user->customer?->license_expiry,
                'address' => $user->customer?->address,
                'valid_ID' => $user->customer?->valid_ID,
            ];
        })->toArray();

        return view('staff.staff_customers', [
            'customers' => $customersData,
            'pagination' => $customers
        ]);
    }
}