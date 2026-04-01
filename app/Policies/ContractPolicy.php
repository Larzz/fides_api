<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
	public function view(User $user, Contract $contract): bool
	{
		return $user->can('view', $contract->company);
	}

	public function update(User $user, Contract $contract): bool
	{
		return $user->hasRole('admin');
	}

	public function delete(User $user, Contract $contract): bool
	{
		return $user->hasRole('admin');
	}
}
