<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DoctorAuthController extends Controller
{
    // Show the login form for doctors
    public function showLoginForm()
    {
        return view('auth.doctor_login');
    }

    // Process doctor login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('doctor')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Show the registration form for doctors
    public function showRegisterForm()
    {
        return view('doctor.auth.register');
    }

    // Process doctor registration
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:doctors',
            'email'     => 'required|string|email|max:255|unique:doctors',
            'mobile'    => 'required|string|max:35|unique:doctors',
            'password'  => 'required|string|confirmed|min:8',
            // Add validation rules for other fields as needed
        ]);

        $doctor = Doctor::create([
            'name'      => $request->name,
            'user_name' => $request->user_name,
            'email'     => $request->email,
            'mobile'    => $request->mobile,
            'password'  => Hash::make($request->password),
            // Initialize other fields if needed
        ]);

        // Optionally, assign a default role using Spatie Permission:
        // $doctor->assignRole('doctor');

        Auth::guard('doctor')->login($doctor);

        return redirect()->route('doctor.dashboard');
    }

    // Log the doctor out
    public function logout(Request $request)
    {
        Auth::guard('doctor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('doctor.login');
    }
}
