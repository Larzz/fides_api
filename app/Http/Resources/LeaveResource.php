<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'user_id' => $this->user_id,
			'user' => new UserResource($this->whenLoaded('user')),
			'start_date' => $this->start_date?->format('Y-m-d'),
			'end_date' => $this->end_date?->format('Y-m-d'),
			'status' => $this->status,
			'type' => $this->type,
			'reason' => $this->reason,
			'description' => $this->description,
			'notes' => $this->notes,
			'leave_notes' => LeaveNoteResource::collection($this->whenLoaded('leaveNotes')),
			'status_details' => $this->whenLoaded('statusRelation', function () {
				return [
					'name' => $this->statusRelation->name,
					'description' => $this->statusRelation->description,
				];
			}),
			'created_at' => $this->created_at?->toISOString(),
			'updated_at' => $this->updated_at?->toISOString(),
		];
	}
}

