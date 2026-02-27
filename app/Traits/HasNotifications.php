<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HasNotifications
{
	/**
	 * Create a notification for the model
	 */
	public function createNotification(string $notification, string $description, ?int $userId = null, ?string $remarks = null): int
	{
		$user = Auth::user();
		$tableName = $this->getTable();
		$notificationTable = $tableName.'_system_notifications';

		$notificationId = DB::table($notificationTable)->insertGetId([
			'notification' => $notification,
			'description' => $description,
			$this->getNotificationForeignKey() => $this->getKey(),
			'user_id' => $userId ?? $user?->id,
			'remarks' => $remarks ?? '',
			'created_at' => now(),
			'updated_at' => now(),
		]);

		return $notificationId;
	}

	/**
	 * Mark notification as read
	 */
	public function markNotificationAsRead(int $notificationId, ?int $userId = null): void
	{
		$user = Auth::user();
		$tableName = $this->getTable();
		$readTable = $tableName.'_system_notifications_read';

		DB::table($readTable)->insert([
			'notification_id' => $notificationId,
			$this->getNotificationForeignKey() => $this->getKey(),
			'user_id' => $userId ?? $user?->id,
			'read_at' => now()->toDateTimeString(),
			'read_by' => $user?->id,
			'read_by_name' => $user?->name ?? '',
			'read_by_email' => $user?->email ?? '',
			'read_by_phone' => $user?->phone ?? '',
			'read_by_address' => $user?->address ?? '',
			'read_by_city' => $user?->city ?? '',
			'read_by_state' => $user?->state ?? '',
			'created_at' => now(),
			'updated_at' => now(),
		]);
	}

	/**
	 * Get the foreign key name for the notification table
	 */
	protected function getNotificationForeignKey(): string
	{
		$tableName = $this->getTable();
		// Handle special cases
		if ($tableName === 'users') {
			return 'user_id';
		}
		// For other tables, singularize and add _id
		$singular = rtrim($tableName, 's');
		return $singular.'_id';
	}
}

