<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthTokenController extends Controller
{
    /**
     * Issue a Sanctum API token for a valid user.
     * POST /api/login or /api/token
     */
    public function issueToken(Request $request)
    {
        // Validate incoming email + password
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Find user by email
        $user = User::where('email', $credentials['email'])->first();

        // Check password
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Create Sanctum token
        $token = $user->createToken('api-client')->plainTextToken;

        // Return token + basic user info
        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }
}
