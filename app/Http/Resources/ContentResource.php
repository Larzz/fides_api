<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
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
			'user_id' => $this->user_id,
			'user' => new UserResource($this->whenLoaded('user')),
			'file_name' => $this->file_name,
			'file_path' => $this->file_path ? asset('storage/'.$this->file_path) : null,
			'file_type' => $this->file_type,
			'file_size' => $this->file_size,
			'file_mime_type' => $this->file_mime_type,
			'file_extension' => $this->file_extension,
			'file_hash' => $this->file_hash,
			'category_id' => $this->category_id,
			'category' => new ContentCategoryResource($this->whenLoaded('category')),
			'downloads_count' => $this->whenLoaded('downloads', fn () => $this->downloads->count()),
			'shares_count' => $this->whenLoaded('shares', fn () => $this->shares->count()),
			'created_at' => $this->created_at?->toISOString(),
			'updated_at' => $this->updated_at?->toISOString(),
		];
	}
}

