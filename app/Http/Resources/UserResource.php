<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
			'email' => $this->email,
			'role' => $this->role,
			'status' => $this->status,
			'phone' => $this->phone,
			'address' => $this->address,
			'city' => $this->city,
			'state' => $this->state,
			'zip' => $this->zip,
			'country' => $this->country,
			'image' => $this->image ? asset('storage/'.$this->image) : null,
			'role_details' => $this->whenLoaded('roleRelation', function () {
				return [
					'name' => $this->roleRelation->name,
					'description' => $this->roleRelation->description,
				];
			}),
			'status_details' => $this->whenLoaded('statusRelation', function () {
				return [
					'name' => $this->statusRelation->name,
					'description' => $this->statusRelation->description,
				];
			}),
			'notes' => UserNoteResource::collection($this->whenLoaded('notes')),
			'images' => UserImageResource::collection($this->whenLoaded('images')),
			'created_at' => $this->created_at?->toISOString(),
			'updated_at' => $this->updated_at?->toISOString(),
		];
	}
}

