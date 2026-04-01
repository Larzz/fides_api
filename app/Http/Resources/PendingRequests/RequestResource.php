<?php

namespace App\Http\Resources\PendingRequests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full or summary JSON representation of a {@see \App\Models\ServiceRequest}.
 */
class RequestResource extends JsonResource
{
	/**
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		$status = $this->status;
		$statusValue = $status instanceof \BackedEnum ? $status->value : $status;

		return [
			'id' => $this->id,
			'request_type' => $this->whenLoaded('requestType', function () {
				return [
					'id' => $this->requestType->id,
					'name' => $this->requestType->name,
				];
			}),
			'user' => UserResource::make($this->whenLoaded('user')),
			'details' => $this->details,
			'submitted_at' => $this->submitted_at?->toIso8601String(),
			'submitted_at_human' => $this->submitted_at?->diffForHumans(),
			'status' => $statusValue,
			'notes' => $this->notes,
			'reviewed_at' => $this->reviewed_at?->toIso8601String(),
			'reviewer' => UserResource::make($this->whenLoaded('reviewer')),
			'attachments' => RequestAttachmentResource::collection(
				$this->whenLoaded('attachments')
			),
		];
	}
}
