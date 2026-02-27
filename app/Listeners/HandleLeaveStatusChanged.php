<?php

namespace App\Listeners;

use App\Events\LeaveStatusChanged;
use Illuminate\Support\Facades\Log;

class HandleLeaveStatusChanged
{
	/**
	 * Handle the event.
	 */
	public function handle(LeaveStatusChanged $event): void
	{
		$leave = $event->leave;
		$oldStatus = $event->oldStatus;
		$newStatus = $event->newStatus;

		Log::info("Leave status changed", [
			'leave_id' => $leave->id,
			'leave_name' => $leave->name,
			'old_status' => $oldStatus,
			'new_status' => $newStatus,
		]);

		$leave->logActivity(
			'status_changed',
			"Leave status changed from '{$oldStatus}' to '{$newStatus}'",
			[
				'old_status' => $oldStatus,
				'new_status' => $newStatus,
			]
		);
	}
}

