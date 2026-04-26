<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManageUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $customers = Customer::all();

        return view('admin.admin_users', compact('users', 'customers'));
    }

    public function show(User $user)
    {
        $staffData = null;
        $customerData = null;
        
        if ($user->role === 'staff') {
            $staffData = $user->staff;
        } elseif ($user->role === 'customer') {
            $customerData = $user->customer;
        }

        return response()->json([
            'user' => $user,
            'staff' => $staffData,
            'customer' => $customerData,
            'full_name' => trim($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name)
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name'   => 'required|string|max:255',
                'middle_name'  => 'nullable|string|max:255',
                'last_name'    => 'required|string|max:255',
                'username'     => 'required|string|unique:users,username',
                'email'        => 'required|email|unique:users,email',
                'phone_number' => 'required|string|unique:users,phone_number',
                'role'         => 'required|string|in:admin,staff',
                'password'     => 'required|string|min:6',
                // staff-specific fields
                'employee_no'  => 'required_if:role,staff|nullable|string|unique:staffs,employee_no',
                'position'     => 'nullable|string|max:255',
                'hired_at'     => 'nullable|date',
            ]);

            // Step 1: Create the User (superclass)
            $user = User::create([
                'first_name'   => $validated['first_name'],
                'middle_name'  => $validated['middle_name'] ?? null,
                'last_name'    => $validated['last_name'],
                'username'     => $validated['username'],
                'email'        => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'role'         => $validated['role'],
                'password'     => Hash::make($validated['password']),  // Explicitly hash with Hash::make()
                'status'       => 'active',
            ]);

            // Step 2: If staff, create the Staff subclass record using the same user_ID
            if ($user->role === 'staff') {
                Staff::create([
                    'user_ID'     => $user->user_ID,
                    'employee_no' => $validated['employee_no'],
                    'position'    => $validated['position'] ?? null,
                    'hired_at'    => $validated['hired_at'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user'    => $user
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'username'     => 'required|string|unique:users,username,' . $user->user_ID . ',user_ID',
            'email'        => 'required|email|unique:users,email,' . $user->user_ID . ',user_ID',
            'phone_number' => 'required|string|unique:users,phone_number,' . $user->user_ID . ',user_ID',
            'role'         => 'required|string|in:admin,staff',
            'status'       => 'required|string|in:active,inactive',
            'password'     => 'nullable|string|min:6',
            // staff-specific fields
            'employee_no'  => 'required_if:role,staff|nullable|string|unique:staffs,employee_no,' . $user->user_ID . ',user_ID',
            'position'     => 'nullable|string|max:255',
            'hired_at'     => 'nullable|date',
        ]);

// Update password only if provided and hash it
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

        // Step 1: Update the User (superclass)
        $user->update(collect($validated)->only([
            'first_name', 'middle_name', 'last_name',
            'username', 'email', 'phone_number',
            'role', 'status', 'password'
        ])->toArray());

        // Step 2: Update or create the Staff subclass record
        if ($user->role === 'staff') {
            Staff::updateOrCreate(
                ['user_ID' => $user->user_ID],  // find by user_ID
                [
                    'employee_no' => $validated['employee_no'],
                    'position'    => $validated['position'] ?? null,
                    'hired_at'    => $validated['hired_at'] ?? null,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user'    => $user
        ]);
    }

    public function destroy(User $user)
    {
        // Deleting the user will cascade delete the staff record automatically
        // because of the onDelete('cascade') on the staffs table
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}