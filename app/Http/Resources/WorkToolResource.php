<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkToolResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'category' => $this->category,
			'billing_type' => $this->billing_type,
			'cost' => (string) $this->cost,
			'currency' => $this->currency,
			'renewal_date' => $this->renewal_date?->format('Y-m-d'),
			'status' => $this->status,
			'notes' => $this->notes,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		];
	}
}
