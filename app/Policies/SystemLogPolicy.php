<?php

namespace App\Policies;

use App\Models\SystemLog;
use App\Models\User;

class SystemLogPolicy
{
	public function viewAny(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}

	public function view(User $user, SystemLog $systemLog): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}

	public function export(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}
}
