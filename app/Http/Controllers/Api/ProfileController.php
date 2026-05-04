<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get the authenticated student's profile.
     * GET /api/profile
     */
    public function show(Request $request)
    {
        return response()->json([
            'student' => $request->user(),
        ]);
    }

    /**
     * Update the student's profile details.
     * PUT /api/profile
     */
    public function update(Request $request)
    {
        $student = $request->user();

        $validated = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'faculty' => 'sometimes|string|max:255',
            'year'    => 'sometimes|integer|min:1|max:4',
            'phone'   => 'sometimes|string|max:20',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'student' => $student->fresh(),
        ]);
    }

    /**
     * Upload a profile avatar.
     * POST /api/profile/avatar
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $student = $request->user();

        // Delete old avatar if exists
        if ($student->avatar) {
            Storage::disk('public')->delete($student->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $student->update(['avatar' => $path]);

        return response()->json([
            'message'    => 'Avatar uploaded successfully',
            'avatar_url' => asset('storage/' . $path),
        ]);
    }

    /**
     * Change the student's password.
     * PUT /api/profile/password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $student = $request->user();

        if (! Hash::check($request->current_password, $student->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $student->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }
}
