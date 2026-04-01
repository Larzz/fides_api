<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'title' => $this->title,
			'category' => $this->category,
			'file_path' => $this->file_path,
			'uploaded_by' => $this->uploaded_by,
			'notify_stakeholders' => (bool) $this->notify_stakeholders,
			'archived_at' => $this->archived_at,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'last_updated' => $this->updated_at,
			'uploader' => new UserResource($this->whenLoaded('uploader')),
			'shares' => FileShareResource::collection($this->whenLoaded('shares')),
		];
	}
}
