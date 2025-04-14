<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email"unique:profiles',
            'password' => 'required|min:6',
            'fullName' => 'required:string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string'
        ]);

        $user = Profile::create([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'fullName' => $validated['fullName'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null
        ]);

        return response()->json(['message' => 'User registered'],201);

    }

    public function login(Request $request){
        $credentials = $request->only('email','password');

        if(Auth::guard('web')->attempt($credentials)){
            $request->session()->regenerate();

            return response()->json(['message' => 'User logged in successfully']);
        }
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
