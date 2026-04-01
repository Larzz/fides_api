<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class ClientPolicy
{
	public function viewAny(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee', 'client']);
	}

	public function view(User $user, Company $company): bool
	{
		if ($user->hasRole('admin')) {
			return true;
		}

		if ($user->hasRole('employee')) {
			return $company->users()->where('users.id', $user->id)->exists();
		}

		if ($user->hasRole('client')) {
			return $user->companies()->where('companies.id', $company->id)->exists();
		}

		return false;
	}

	public function create(User $user): bool
	{
		return $user->hasRole('admin');
	}

	public function update(User $user, Company $company): bool
	{
		return $user->hasRole('admin');
	}

	public function delete(User $user, Company $company): bool
	{
		return $user->hasRole('admin');
	}

	public function assignEmployees(User $user, Company $company): bool
	{
		return $user->hasRole('admin');
	}

	public function viewMetrics(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}

	public function export(User $user): bool
	{
		return $user->hasRole('admin');
	}

	public function manageContracts(User $user, Company $company): bool
	{
		return $user->hasRole('admin');
	}
}
