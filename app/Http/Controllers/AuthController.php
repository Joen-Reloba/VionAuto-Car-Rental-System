<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->redirectUrlForRole(Auth::user()->role));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role;

            // Redirect based on role
            return redirect($this->redirectUrlForRole($role));
        }

        return back()->withErrors([
            'username' => 'Invalid username or password.',
        ])->onlyInput('username');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:8|confirmed',
            'license_no' => 'required|string|unique:customers,license_no|max:255',
            'license_expiry' => 'required|date',
            'valid_id' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'agree_terms' => 'required|accepted',
        ]);

        // Handle file upload
        $validIdPath = null;
        if ($request->hasFile('valid_id')) {
            $file = $request->file('valid_id');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/valid-ids'), $filename);
            $validIdPath = $filename;
        }

        // Create user
        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'role' => 'customer',
            'status' => 'active',
        ]);

        // Create customer record
        $user->customer()->create([
            'address' => $validated['address'],
            'birthday' => $validated['birthday'],
            'license_no' => $validated['license_no'],
            'license_expiry' => $validated['license_expiry'],
            'valid_ID' => $validIdPath,
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in with your credentials.');
    }

    private function redirectUrlForRole(string $role): string
    {
        return match ($role) {
            'admin' => session('last_admin_url', route('admin.dashboard')),
            'staff' => session('last_staff_url', route('staff.vehicles')),
            'customer' => route('landing'),
            default => route('landing'),
        };
    }
}
