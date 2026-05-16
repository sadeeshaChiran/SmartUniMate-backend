<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new student.
     * POST /api/register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:students,email',
            'password'   => 'required|string|min:8|confirmed',
            'student_id' => 'nullable|string|unique:students,student_id',
            'faculty'    => 'nullable|string|max:255',
            'year'       => 'nullable|integer|min:1|max:4',
            'phone'      => 'nullable|string|max:20',
        ]);

        $student = Student::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'student_id' => $validated['student_id'] ?? null,
            'faculty'    => $validated['faculty'] ?? null,
            'year'       => $validated['year'] ?? null,
            'phone'      => $validated['phone'] ?? null,
        ]);

        $token = $student->createToken('smartunimate')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'student' => $student,
            'token'   => $token,
        ], 201);
    }

    /**
     * Login an existing student.
     * POST /api/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $student = Student::where('email', $request->email)->first();

        if (! $student || ! Hash::check($request->password, $student->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke old tokens and issue a fresh one
        $student->tokens()->delete();
        $token = $student->createToken('smartunimate')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'student' => $student,
            'token'   => $token,
        ]);
    }

    /**
     * Logout the current student.
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
