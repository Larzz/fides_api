<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
	public function __construct(
		protected UserRepository $userRepository
	) {
	}

	/**
	 * Get all users
	 */
	public function getAllUsers(int $perPage = 15): LengthAwarePaginator
	{
		return $this->userRepository->getWithRelations(['roleRelation', 'statusRelation', 'notes', 'images'], $perPage);
	}

	/**
	 * Get user by ID
	 */
	public function getUserById(int $id): User
	{
		return $this->userRepository->findOrFail($id);
	}

	/**
	 * Create a new user
	 */
	public function createUser(array $data): User
	{
		return DB::transaction(function () use ($data) {
			if (isset($data['password'])) {
				$data['password'] = Hash::make($data['password']);
			}

			$user = $this->userRepository->create($data);

			$user->logActivity('user_created', 'User account created', [
				'user_name' => $user->name,
				'user_email' => $user->email,
			]);

			return $user;
		});
	}

	/**
	 * Update user
	 */
	public function updateUser(int $id, array $data): User
	{
		return DB::transaction(function () use ($id, $data) {
			if (isset($data['password'])) {
				$data['password'] = Hash::make($data['password']);
			}

			$this->userRepository->update($id, $data);
			$user = $this->userRepository->findOrFail($id);

			$user->logActivity('user_updated', 'User account updated', [
				'user_name' => $user->name,
				'user_email' => $user->email,
			]);

			return $user;
		});
	}

	/**
	 * Delete user
	 */
	public function deleteUser(int $id): bool
	{
		return DB::transaction(function () use ($id) {
			$user = $this->userRepository->findOrFail($id);
			$user->logActivity('user_deleted', 'User account deleted', [
				'user_name' => $user->name,
				'user_email' => $user->email,
			]);

			return $this->userRepository->delete($id);
		});
	}

	/**
	 * Assign role to user
	 */
	public function assignRole(int $userId, string $role): User
	{
		return DB::transaction(function () use ($userId, $role) {
			$user = $this->userRepository->findOrFail($userId);
			$user->update(['role' => $role]);

			$user->logActivity('role_assigned', "Role '{$role}' assigned to user", [
				'role' => $role,
			]);

			return $user->fresh();
		});
	}

	/**
	 * Assign status to user
	 */
	public function assignStatus(int $userId, string $status): User
	{
		return DB::transaction(function () use ($userId, $status) {
			$user = $this->userRepository->findOrFail($userId);
			$user->update(['status' => $status]);

			$user->logActivity('status_assigned', "Status '{$status}' assigned to user", [
				'status' => $status,
			]);

			return $user->fresh();
		});
	}

	/**
	 * Upload profile image
	 */
	public function uploadProfileImage(int $userId, $file): User
	{
		return DB::transaction(function () use ($userId, $file) {
			$user = $this->userRepository->findOrFail($userId);

			$path = $file->store('users/images', 'public');
			$user->update(['image' => $path]);

			$user->images()->create(['image' => $path]);

			$user->logActivity('image_uploaded', 'Profile image uploaded', [
				'image_path' => $path,
			]);

			return $user->fresh();
		});
	}

	/**
	 * Add note to user
	 */
	public function addNote(int $userId, string $note): User
	{
		return DB::transaction(function () use ($userId, $note) {
			$user = $this->userRepository->findOrFail($userId);
			$user->notes()->create(['note' => $note]);

			$user->logActivity('note_added', 'Note added to user', [
				'note' => $note,
			]);

			return $user->fresh();
		});
	}

	/**
	 * Search users
	 */
	public function searchUsers(string $query, int $perPage = 15): LengthAwarePaginator
	{
		return $this->userRepository->search($query, [], $perPage);
	}

	/**
	 * Get users by role
	 */
	public function getUsersByRole(string $role, int $perPage = 15): LengthAwarePaginator
	{
		return $this->userRepository->getByRole($role, $perPage);
	}

	/**
	 * Get users by status
	 */
	public function getUsersByStatus(string $status, int $perPage = 15): LengthAwarePaginator
	{
		return $this->userRepository->getByStatus($status, $perPage);
	}
}

