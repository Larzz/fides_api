<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserContentUpload;

class ContentPolicy
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
	public function view(User $user, UserContentUpload $content): bool
	{
		return true;
	}

	/**
	 * Determine if the user can create models.
	 */
	public function create(User $user): bool
	{
		return true;
	}

	/**
	 * Determine if the user can delete the model.
	 */
	public function delete(User $user, UserContentUpload $content): bool
	{
		return $user->isAdmin() || $user->isStaff() || $user->id === $content->user_id;
	}

	/**
	 * Determine if the user can download the content.
	 */
	public function download(User $user, UserContentUpload $content): bool
	{
		return true;
	}

	/**
	 * Determine if the user can share the content.
	 */
	public function share(User $user, UserContentUpload $content): bool
	{
		return $user->id === $content->user_id || $user->isAdmin() || $user->isStaff();
	}
}

