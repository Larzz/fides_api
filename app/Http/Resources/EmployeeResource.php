<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'role' => $this->role,
			'job_title' => $this->job_title,
			'status' => $this->status,
			'last_active_at' => $this->last_active_at,
			'avatar' => $this->avatar,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'companies' => CompanyResource::collection($this->whenLoaded('companies')),
		];
	}
}
