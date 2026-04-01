<?php

namespace App\Http\Resources\PendingRequests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User shape returned by Pending Requests API endpoints.
 */
class UserResource extends JsonResource
{
	/**
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'avatar' => $this->avatar,
			'role' => $this->role,
		];
	}
}
