<?php

namespace App\Policies;

use App\Models\Access;
use App\Models\User;

class AccessPolicy
{
	public function viewAny(User $user): bool
	{
		return $user->hasRole('admin');
	}

	public function view(User $user, Access $access): bool
	{
		return $user->hasRole('admin');
	}

	public function create(User $user): bool
	{
		return $user->hasRole('admin');
	}

	public function update(User $user, Access $access): bool
	{
		return $user->hasRole('admin');
	}

	public function delete(User $user, Access $access): bool
	{
		return $user->hasRole('admin');
	}

	public function assignUsers(User $user, Access $access): bool
	{
		return $user->hasRole('admin');
	}
}
