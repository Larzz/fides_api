<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
	/**
	 * Determine if the user can view any models.
	 */
	public function viewAny(User $user): bool
	{
		return $user->isAdmin() || $user->isManager();
	}

	/**
	 * Determine if the user can view the model.
	 */
	public function view(User $user, User $model): bool
	{
		return $user->isAdmin() || $user->isManager() || $user->id === $model->id;
	}

	/**
	 * Determine if the user can create models.
	 */
	public function create(User $user): bool
	{
		return $user->isAdmin() || $user->isManager();
	}

	/**
	 * Determine if the user can update the model.
	 */
	public function update(User $user, User $model): bool
	{
		return $user->isAdmin() || $user->isManager() || $user->id === $model->id;
	}

	/**
	 * Determine if the user can delete the model.
	 */
	public function delete(User $user, User $model): bool
	{
		return $user->isAdmin();
	}

	/**
	 * Determine if the user can assign roles.
	 */
	public function assignRole(User $user): bool
	{
		return $user->isAdmin();
	}

	/**
	 * Determine if the user can assign status.
	 */
	public function assignStatus(User $user): bool
	{
		return $user->isAdmin() || $user->isManager();
	}
}

