<?php

namespace App\Listeners;

use App\Events\ToolStatusChanged;
use Illuminate\Support\Facades\Log;

class HandleToolStatusChanged
{
	/**
	 * Handle the event.
	 */
	public function handle(ToolStatusChanged $event): void
	{
		$tool = $event->tool;
		$oldStatus = $event->oldStatus;
		$newStatus = $event->newStatus;

		Log::info("Tool status changed", [
			'tool_id' => $tool->id,
			'tool_name' => $tool->name,
			'old_status' => $oldStatus,
			'new_status' => $newStatus,
		]);

		$tool->logActivity(
			'status_changed',
			"Tool status changed from '{$oldStatus}' to '{$newStatus}'",
			[
				'old_status' => $oldStatus,
				'new_status' => $newStatus,
			]
		);

		$tool->createNotification(
			'tool_status_changed',
			"Tool '{$tool->name}' status changed from '{$oldStatus}' to '{$newStatus}'",
			$tool->user_id
		);
	}
}

