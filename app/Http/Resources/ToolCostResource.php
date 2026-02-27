<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ToolCostResource extends JsonResource
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
			'date' => $this->date?->format('Y-m-d'),
			'created_at' => $this->created_at?->toISOString(),
			'updated_at' => $this->updated_at?->toISOString(),
		];
	}
}

