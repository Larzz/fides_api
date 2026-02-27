<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ToolBillingResource extends JsonResource
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
			'description' => $this->description,
			'amount' => (float) $this->amount,
			'currency' => $this->currency,
			'billing_date' => $this->billing_date?->format('Y-m-d'),
			'billing_period' => $this->billing_period,
			'created_at' => $this->created_at?->toISOString(),
			'updated_at' => $this->updated_at?->toISOString(),
		];
	}
}

