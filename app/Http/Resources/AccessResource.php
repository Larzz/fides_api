<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccessResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'company_id' => $this->company_id,
			'platform' => $this->platform,
			'status' => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'company' => new CompanyResource($this->whenLoaded('company')),
			'users' => UserResource::collection($this->whenLoaded('users')),
		];
	}
}
