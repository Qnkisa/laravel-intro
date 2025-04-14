<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if the user exists
        $user = Profile::where('email', $credentials['email'])->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check the password with Hash::check
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Delete the token that was used to authenticate the request
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request){
        return response()->json(Auth::user());
    }
}
