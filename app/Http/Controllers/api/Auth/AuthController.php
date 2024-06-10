<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        try {

            $request->authenticate();
            $user = auth()->user();
            $token = $request->user()->createToken('authToken')->plainTextToken;
            return response()->json([
                'message' => 'login successful',
                'access_token' => $token,
                'user' => $user,
            ]);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            // Handle authentication errors
            return response()->json([
                'error' => 'Authentication failed'
            ], 401);
        } catch (\Exception $e) {
            // Handle other unexpected errors
            return response()->json([
                'error' => 'An unexpected error occurred ' . $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request): JsonResponse
    {
        try {

            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string',
                'role' => 'required|string',
            ]);

            $user = User::Create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);

            return response()->json([
                'message' => 'Registered Successfully',
                'user' => $user,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'error' => 'Validation failed',
                'errors'=> $e->validator->getMessageBag()
            ], 422);
        }catch(\Exception $e){
         // Handle other unexpected errors
         return response()->json([
            'error' => 'An unexpected error occurred ' .$e->getMessage()
         ], 500);
        }
    }
}
