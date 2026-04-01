<?php

namespace App\Http\Resources\PendingRequests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Attachment metadata for a service request.
 */
class RequestAttachmentResource extends JsonResource
{
	/**
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'file_name' => $this->file_name,
			'file_path' => $this->file_path,
			'file_size' => $this->file_size,
			'mime_type' => $this->mime_type,
			'created_at' => $this->created_at,
		];
	}
}
