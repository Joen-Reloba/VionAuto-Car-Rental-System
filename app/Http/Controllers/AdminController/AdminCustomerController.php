<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->with('customer')
            ->get()
            ->map(function ($user) {
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
            })
            ->toArray();

        return view('admin.admin_customers', compact('customers'));
    }
}