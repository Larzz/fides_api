<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\DashboardNotification;
use App\Models\User;

class ContractNotificationService
{
	public static function notifyIfNeeded(
		Contract $contract,
		?string $previousStatus,
		string $newStatus
	): void {
		if ($previousStatus !== null && $previousStatus === $newStatus) {
			return;
		}

		if (!in_array($newStatus, ['expiring', 'expired'], true)) {
			return;
		}

		$contract->loadMissing('company');
		$company = $contract->company;
		$company?->loadMissing('users');

		$userIds = User::query()
			->where('role', 'admin')
			->pluck('id')
			->merge($company?->users?->pluck('id') ?? collect())
			->unique()
			->filter()
			->values();

		$title = $newStatus === 'expired'
			? 'Contract expired'
			: 'Contract expiring soon';
		$message = sprintf(
			'"%s" (%s) is now %s.',
			$contract->title,
			$company?->name ?? 'client',
			$newStatus === 'expired' ? 'expired' : 'expiring within 30 days'
		);

		$type = $newStatus === 'expired'
			? 'contract_expired'
			: 'contract_expiring';

		$userIds->each(function ($userId) use ($title, $message, $type) {
			DashboardNotification::create([
				'user_id' => $userId,
				'title' => $title,
				'message' => $message,
				'type' => $type,
				'created_at' => now(),
			]);
		});
	}
}
