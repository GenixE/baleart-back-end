<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Fetch role_id for the 'visitant' role
        $role = Role::where('name', 'visitant')->first();
        if (!$role) {
            return response()->json(['errors' => ['role_name' => 'Role not found']], 422);
        }

        // Create new user
        $user = User::create([
            'name' => $request->name,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $role->id
        ]);

        // Return success response
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'User registered successfully'
        ], 201);
    }

    public function login(Request $request)
    {
        // Check if API key is provided in the header
        $apiKey = $request->header('X-API-KEY');

        if ($apiKey) {
            // Validate the provided API key
            if ($apiKey === env('API_KEY')) {
                // Create a direct token for the API key without a database user
                $token = Str::random(80); // Generate a random token
                PersonalAccessToken::create([
                    'tokenable_type' => 'App\Models\ApiKey', // Use a dummy model or reference
                    'tokenable_id' => 1, // Can be any dummy ID since no user is attached
                    'name' => 'api_key_token',
                    'token' => hash('sha256', $token),
                    'abilities' => ['*'], // Allow full access or restrict as needed
                ]);

                return response()->json([
                    'message' => 'Login successful via API key',
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ]);
            }

            return response()->json(['message' => 'Invalid API key'], 401);
        }

        // Handle email/password login
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
