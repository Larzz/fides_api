<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ToolResource extends JsonResource
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
			'image' => $this->image ? asset('storage/'.$this->image) : null,
			'url' => $this->url,
			'category' => $this->category,
			'subcategory' => $this->subcategory,
			'status' => $this->status,
			'user_id' => $this->user_id,
			'user' => new UserResource($this->whenLoaded('user')),
			'category_details' => $this->whenLoaded('categoryRelation', function () {
				return [
					'name' => $this->categoryRelation->name,
					'description' => $this->categoryRelation->description,
				];
			}),
			'status_details' => $this->whenLoaded('statusRelation', function () {
				return [
					'name' => $this->statusRelation->name,
					'description' => $this->statusRelation->description,
				];
			}),
			'users' => UserResource::collection($this->whenLoaded('users')),
			'tags' => ToolTagResource::collection($this->whenLoaded('toolTags')),
			'notes' => ToolNoteResource::collection($this->whenLoaded('toolNotes')),
			'costs' => ToolCostResource::collection($this->whenLoaded('toolCosts')),
			'billings' => ToolBillingResource::collection($this->whenLoaded('toolBillings')),
			'created_at' => $this->created_at?->toISOString(),
			'updated_at' => $this->updated_at?->toISOString(),
		];
	}
}

