<?php

namespace App\Events;

use App\Models\Tool;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToolStatusChanged
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * Create a new event instance.
	 */
	public function __construct(
		public Tool $tool,
		public string $oldStatus,
		public string $newStatus
	) {
	}
}

