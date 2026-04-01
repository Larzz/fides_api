<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'primary_contact_name' => $this->primary_contact_name,
			'primary_contact_email' => $this->primary_contact_email,
			'logo' => $this->logo,
			'status' => $this->status,
			'assigned_at' => optional($this->pivot)->assigned_at,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
