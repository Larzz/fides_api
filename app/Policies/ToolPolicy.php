<?php

namespace App\Policies;

use App\Models\Tool;
use App\Models\User;

class ToolPolicy
{
	/**
	 * Determine if the user can view any models.
	 */
	public function viewAny(User $user): bool
	{
		return true;
	}

	/**
	 * Determine if the user can view the model.
	 */
	public function view(User $user, Tool $tool): bool
	{
		return true;
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
	public function update(User $user, Tool $tool): bool
	{
		return $user->isAdmin() || $user->isManager();
	}

	/**
	 * Determine if the user can delete the model.
	 */
	public function delete(User $user, Tool $tool): bool
	{
		return $user->isAdmin();
	}

	/**
	 * Determine if the user can assign users to tool.
	 */
	public function assignUsers(User $user): bool
	{
		return $user->isAdmin() || $user->isManager();
	}
}

