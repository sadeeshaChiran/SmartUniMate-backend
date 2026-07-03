<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Login an existing admin.
     * POST /api/v1/admin/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = User::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid admin credentials.'],
            ]);
        }

        // Revoke old tokens and issue a fresh one with 'role:admin' ability
        $admin->tokens()->delete();
        $token = $admin->createToken('smartunimate-admin', ['role:admin'])->plainTextToken;

        return response()->json([
            'message' => 'Admin login successful',
            'admin'   => $admin,
            'token'   => $token,
        ]);
    }

    /**
     * Logout the current admin.
     * POST /api/v1/admin/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Admin logged out successfully']);
    }
}
