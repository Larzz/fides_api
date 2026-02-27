<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
	/**
	 * Register a new user
	 */
	public function register(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
			'role' => ['nullable', 'string', 'in:Admin,Manager,Staff'],
		]);

		$validated['password'] = Hash::make($validated['password']);
		$validated['role'] = $validated['role'] ?? 'Staff';
		$validated['status'] = 'active';

		$user = \App\Models\User::create($validated);
		$token = $user->createToken('auth_token')->plainTextToken;

		return ApiResponse::created([
			'user' => new UserResource($user),
			'token' => $token,
			'token_type' => 'Bearer',
		], 'User registered successfully');
	}

	/**
	 * Login user
	 */
	public function login(Request $request): JsonResponse
	{
		$request->validate([
			'email' => ['required', 'email'],
			'password' => ['required'],
		]);

		if (!Auth::attempt($request->only('email', 'password'))) {
			throw ValidationException::withMessages([
				'email' => ['The provided credentials are incorrect.'],
			]);
		}

		$user = Auth::user();
		$token = $user->createToken('auth_token')->plainTextToken;

		return ApiResponse::success([
			'user' => new UserResource($user),
			'token' => $token,
			'token_type' => 'Bearer',
		], 'Login successful');
	}

	/**
	 * Logout user
	 */
	public function logout(Request $request): JsonResponse
	{
		$request->user()->currentAccessToken()->delete();

		return ApiResponse::success(null, 'Logged out successfully');
	}

	/**
	 * Get authenticated user
	 */
	public function me(Request $request): JsonResponse
	{
		return ApiResponse::success(
			new UserResource($request->user()->load(['roleRelation', 'statusRelation'])),
			'User retrieved successfully'
		);
	}
}

