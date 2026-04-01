<?php

namespace App\Policies;

use App\Enums\RequestWorkflowStatus;
use App\Models\ServiceRequest;
use App\Models\User;

/**
 * Authorization for {@see ServiceRequest} (admin vs employee workflows).
 */
class ServiceRequestPolicy
{
	/**
	 * Admin listing of all submissions.
	 */
	public function viewAny(User $user): bool
	{
		return $user->hasRole('admin');
	}

	/**
	 * View a single submission (admin or owner).
	 */
	public function view(User $user, ServiceRequest $serviceRequest): bool
	{
		if ($user->hasRole('admin')) {
			return true;
		}

		return $user->id === $serviceRequest->user_id;
	}

	/**
	 * Employees create their own submissions via /my-requests.
	 */
	public function create(User $user): bool
	{
		return $user->hasAnyRole(['admin', 'employee']);
	}

	/**
	 * Patch status / review fields.
	 */
	public function updateStatus(User $user, ServiceRequest $serviceRequest): bool
	{
		return $user->hasRole('admin');
	}

	/**
	 * Admins may delete any request; employees only their own pending rows.
	 */
	public function delete(User $user, ServiceRequest $serviceRequest): bool
	{
		if ($user->hasRole('admin')) {
			return true;
		}

		return $user->id === $serviceRequest->user_id
			&& $serviceRequest->status === RequestWorkflowStatus::Pending;
	}
}
