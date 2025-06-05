<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Login
     * 
     * This method is used to login a user
     * 
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @unauthenticated
     * 
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Register
     * 
     * This method is used to register a user
     * 
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @unauthenticated
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($request->hasFile('profile_photo')) { 
                $file = $request->file('profile_photo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('profile_photos', $fileName, 'public');

                $user->media()->create([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'url' => asset('storage/' . $filePath),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'custom_properties' => ['type' => 'profile_photo'],
                ]);
            }

            $user->assignRole($request->role ?? 'Traveler');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->pluck('name')->first(),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Me
     * 
     * This method is used to get the current user
     * 
     */
    public function me()
    {
        try {
            $user = JWTAuth::user();

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo' => $user->media->first()->url ?? null,
                    'role' => $user->roles->pluck('name')->first(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User retrieval failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout
     * 
     * This method is used to logout a user
     * 
     */
    public function logout()
    {
        try {
            JWTAuth::logout();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
