<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\PendingRequests\UserResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PendingAuthController extends Controller
{
	/**
	 * Issue a Sanctum personal access token (Bearer) for API clients.
	 */
	public function login(Request $request): JsonResponse
	{
		$credentials = $request->validate([
			'email' => ['required', 'email'],
			'password' => ['required', 'string'],
		]);

		if (!Auth::attempt($credentials)) {
			throw ValidationException::withMessages([
				'email' => ['These credentials do not match our records.'],
			]);
		}

		$user = Auth::user();
		$token = $user->createToken('pending-requests-api')->plainTextToken;

		return ApiResponse::success([
			'user' => (new UserResource($user))->resolve(),
			'token' => $token,
			'token_type' => 'Bearer',
		], 'Authenticated');
	}

	/**
	 * Revoke the token used on this request.
	 */
	public function logout(Request $request): JsonResponse
	{
		$request->user()?->currentAccessToken()?->delete();

		return ApiResponse::success(null, 'Logged out');
	}

	/**
	 * Current authenticated user for the Pending Requests module.
	 */
	public function me(Request $request): JsonResponse
	{
		return ApiResponse::success(
			(new UserResource($request->user()))->resolve(),
			'Profile retrieved'
		);
	}
}
