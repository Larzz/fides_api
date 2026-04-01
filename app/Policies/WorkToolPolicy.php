<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkTool;

class WorkToolPolicy
{
	public function viewAny(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}

	public function view(User $user, WorkTool $workTool): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}

	public function create(User $user): bool
	{
		return $user->hasRole('admin');
	}

	public function update(User $user, WorkTool $workTool): bool
	{
		return $user->hasRole('admin');
	}

	public function delete(User $user, WorkTool $workTool): bool
	{
		return $user->hasRole('admin');
	}
}
