<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\AssignRoleRequest;
use App\Http\Requests\User\UploadImageRequest;
use App\Http\Requests\User\AddNoteRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
	public function __construct(
		protected UserService $userService
	) {
	}

	/**
	 * Display a listing of users
	 */
	public function index(Request $request): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$users = $this->userService->getAllUsers($perPage);

		return ApiResponse::paginated(
			UserResource::collection($users),
			'Users retrieved successfully'
		);
	}

	/**
	 * Store a newly created user
	 */
	public function store(StoreUserRequest $request): JsonResponse
	{
		$user = $this->userService->createUser($request->validated());

		return ApiResponse::created(
			new UserResource($user),
			'User created successfully'
		);
	}

	/**
	 * Display the specified user
	 */
	public function show(int $id): JsonResponse
	{
		$user = $this->userService->getUserById($id);

		return ApiResponse::success(
			new UserResource($user->load(['roleRelation', 'statusRelation', 'notes', 'images'])),
			'User retrieved successfully'
		);
	}

	/**
	 * Update the specified user
	 */
	public function update(UpdateUserRequest $request, int $id): JsonResponse
	{
		$user = $this->userService->updateUser($id, $request->validated());

		return ApiResponse::success(
			new UserResource($user),
			'User updated successfully'
		);
	}

	/**
	 * Remove the specified user
	 */
	public function destroy(int $id): JsonResponse
	{
		$this->userService->deleteUser($id);

		return ApiResponse::success(
			null,
			'User deleted successfully'
		);
	}

	/**
	 * Assign role to user
	 */
	public function assignRole(AssignRoleRequest $request, int $id): JsonResponse
	{
		$user = $this->userService->assignRole($id, $request->validated()['role']);

		return ApiResponse::success(
			new UserResource($user),
			'Role assigned successfully'
		);
	}

	/**
	 * Assign status to user
	 */
	public function assignStatus(Request $request, int $id): JsonResponse
	{
		$request->validate([
			'status' => ['required', 'string'],
		]);

		$user = $this->userService->assignStatus($id, $request->input('status'));

		return ApiResponse::success(
			new UserResource($user),
			'Status assigned successfully'
		);
	}

	/**
	 * Upload profile image
	 */
	public function uploadImage(UploadImageRequest $request, int $id): JsonResponse
	{
		$user = $this->userService->uploadProfileImage($id, $request->file('image'));

		return ApiResponse::success(
			new UserResource($user),
			'Image uploaded successfully'
		);
	}

	/**
	 * Add note to user
	 */
	public function addNote(AddNoteRequest $request, int $id): JsonResponse
	{
		$user = $this->userService->addNote($id, $request->validated()['note']);

		return ApiResponse::success(
			new UserResource($user->load('notes')),
			'Note added successfully'
		);
	}

	/**
	 * Search users
	 */
	public function search(Request $request): JsonResponse
	{
		$request->validate([
			'query' => ['required', 'string', 'min:2'],
		]);

		$perPage = $request->get('per_page', 15);
		$users = $this->userService->searchUsers($request->input('query'), $perPage);

		return ApiResponse::paginated(
			UserResource::collection($users),
			'Users found successfully'
		);
	}

	/**
	 * Get users by role
	 */
	public function getByRole(Request $request, string $role): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$users = $this->userService->getUsersByRole($role, $perPage);

		return ApiResponse::paginated(
			UserResource::collection($users),
			'Users retrieved successfully'
		);
	}

	/**
	 * Get users by status
	 */
	public function getByStatus(Request $request, string $status): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$users = $this->userService->getUsersByStatus($status, $perPage);

		return ApiResponse::paginated(
			UserResource::collection($users),
			'Users retrieved successfully'
		);
	}
}

