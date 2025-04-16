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
        // Validation
        $validated = $request->validate([
            'email' => 'required|email|unique:profiles,email',
            'password' => 'required|string|min:6',
            'fullName' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string'
        ]);

        // Check if the email already exists
        if (Profile::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'Email already in use.'
            ], 400); // 400 Bad Request for validation errors
        }

        // Create user
        $user = Profile::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'fullName' => $validated['fullName'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user // Return user info if needed in frontend
        ], 201); // 201 Created
    }

    public function login(Request $request)
    {
        // Validate credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find user by email
        $user = Profile::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401); // 401 Unauthorized
        }

        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'No authenticated user found. Possible causes:',
                'causes' => [
                    'Missing or incorrect Bearer token in the Authorization header.',
                    'Token has expired or has already been revoked.',
                    'Sanctum middleware not applied to this route.',
                    'User model not configured correctly for Sanctum.',
                ],
                'debug' => [
                    'Authorization Header' => $request->header('Authorization'),
                    'Sanctum Auth Guard' => Auth::guard('sanctum')->check(),
                    'Default Auth Guard' => Auth::check(),
                    'User via request()->user()' => null,
                ],
            ], 401);
        }

        $token = $user->currentAccessToken();

        if (!$token) {
            return response()->json([
                'message' => 'Authenticated user found, but no token to revoke.',
                'user_id' => $user->id,
            ]);
        }

        $token->delete();

        return response()->json([
            'message' => 'Logged out successfully',
            'user_id' => $user->id,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401); 
        }

        return response()->json([
            'message' => 'Authenticated user retrieved successfully',
            'user' => $user
        ]);
    }
}