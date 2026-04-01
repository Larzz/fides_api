<?php

namespace App\Policies;

use App\Models\User;

class EmployeePolicy
{
	public function viewAny(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}

	public function view(User $user, User $employee): bool
	{
		return $user->hasAnyRole(['admin', 'employee']) || $user->id === $employee->id;
	}

	public function create(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}

	public function update(User $user, User $employee): bool
	{
		return $user->hasAnyRole(['admin', 'employee']) || $user->id === $employee->id;
	}

	public function delete(User $user, User $employee): bool
	{
		return $user->hasRole('admin') && $user->id !== $employee->id;
	}

	public function assignClients(User $user, User $employee): bool
	{
		return $user->hasAnyRole(['admin', 'employee']) && $employee->role === 'employee';
	}

	public function updateStatus(User $user, User $employee): bool
	{
		return $user->hasAnyRole(['admin', 'employee']) || $user->id === $employee->id;
	}

	public function export(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}

	public function viewMetrics(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}
}
