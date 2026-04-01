<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemLogResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'user_id' => $this->user_id,
			'action' => $this->action,
			'user_type' => $this->user_type,
			'details' => $this->details,
			'created_at' => $this->created_at,
			'user' => UserResource::make($this->whenLoaded('user')),
		];
	}
}
