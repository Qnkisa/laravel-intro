<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:profiles',
            'password' => 'required|min:6',
            'fullName' => 'required:string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string'
        ]);

        $user = Profile::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'fullName' => $validated['fullName'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null
        ]);

        return response()->json(['message' => 'User registered'],201);

    }

    // public function login(Request $request){
    //     $credentials = $request->only('email','password');

    //     if(Auth::guard('web')->attempt($credentials)){
    //         $request->session()->regenerate();

    //         return response()->json(['message' => 'User logged in successfully'],201);
    //     }
        
    //     return response()->json(['message' => 'Invalid credentials'], 401);
    // }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Check if the user exists
        $user = Profile::where('email', $credentials['email'])->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check the password with Hash::check
        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Wrong password'], 401);
        }
        dd(session()->all());
        // Attempt to log in using the email and password
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(['message' => 'User logged in successfully'], 200);
        }

        return response()->json(['message' => 'Something else went wrong'], 500);
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request){
        return response()->json(Auth::user());
    }
}
