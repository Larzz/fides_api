<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;

class LeavePolicy
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
	public function view(User $user, Leave $leave): bool
	{
		return $user->isAdmin() || $user->isStaff() || $user->id === $leave->user_id;
	}

	/**
	 * Determine if the user can create models.
	 */
	public function create(User $user): bool
	{
		return true;
	}

	/**
	 * Determine if the user can update the model.
	 */
	public function update(User $user, Leave $leave): bool
	{
		return $user->isAdmin() || $user->isStaff() || $user->id === $leave->user_id;
	}

	/**
	 * Determine if the user can delete the model.
	 */
	public function delete(User $user, Leave $leave): bool
	{
		return $user->isAdmin() || $user->isStaff() || ($user->id === $leave->user_id && $leave->status === 'pending');
	}

	/**
	 * Determine if the user can approve the leave.
	 */
	public function approve(User $user): bool
	{
		return $user->isAdmin() || $user->isStaff();
	}

	/**
	 * Determine if the user can reject the leave.
	 */
	public function reject(User $user): bool
	{
		return $user->isAdmin() || $user->isStaff();
	}
}

