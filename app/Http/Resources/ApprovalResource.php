<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'type' => $this->type,
			'user_id' => $this->user_id,
			'title' => $this->title,
			'description' => $this->description,
			'metadata' => $this->metadata,
			'status' => $this->status,
			'approved_by' => $this->approved_by,
			'approved_at' => $this->approved_at,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'user' => new UserResource($this->whenLoaded('user')),
			'approver' => new UserResource($this->whenLoaded('approver')),
		];
	}
}
