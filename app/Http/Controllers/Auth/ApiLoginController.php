<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1. Validasi Input
            $validatedData = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            // 2. Cari User
            $user = User::where('email', $validatedData['email'])->first();

            if (!$user || !Hash::check($validatedData['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            // 3. Generate Token Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            // 4. Response
            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            // Tangani error validasi
            $errors = $e->validator->errors()->getMessages();

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        } catch (\Exception $e) {
            // Tangani error umum
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(), // Hati-hati, jangan tampilkan detail error di production
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            // 1. Pastikan User Terotentikasi
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage(), // Hati-hati, jangan tampilkan detail error di production
            ], 500);
        }
    }
}
