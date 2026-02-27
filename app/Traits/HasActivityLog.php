<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait HasActivityLog
{
	/**
	 * Log an activity for the model
	 */
	public function logActivity(string $activity, string $description, ?array $metadata = null): void
	{
		$user = Auth::user();
		$tableName = $this->getTable();
		$activityTable = $tableName.'_system_activities';

		$activityData = [
			'activity' => $activity,
			'description' => $description,
			$this->getForeignKey() => $this->getKey(),
			'user_id' => $user?->id,
			'user_name' => $user?->name,
			'user_email' => $user?->email,
			'user_phone' => $user?->phone ?? '',
			'user_address' => $user?->address ?? '',
			'user_city' => $user?->city ?? '',
			'user_state' => $user?->state ?? '',
			'user_zip' => $user?->zip ?? '',
			'user_country' => $user?->country ?? '',
			'user_ip_address' => Request::ip(),
			'user_device_type' => Request::header('User-Agent', 'Unknown'),
			'user_device_model' => Request::header('X-Device-Model', ''),
			'user_device_manufacturer' => Request::header('X-Device-Manufacturer', ''),
			'user_device_name' => Request::header('X-Device-Name', ''),
		];

		if ($metadata) {
			$activityData = array_merge($activityData, $metadata);
		}

		\DB::table($activityTable)->insert($activityData);
	}

	/**
	 * Get the foreign key name for the activity table
	 */
	protected function getForeignKey(): string
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

