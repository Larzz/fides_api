<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileShareResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'file_id' => $this->file_id,
			'share_all_employees' => (bool) $this->share_all_employees,
			'shared_with_user_id' => $this->shared_with_user_id,
			'shared_with_company_id' => $this->shared_with_company_id,
			'notifications_enabled' => $this->notifications_enabled,
			'created_at' => $this->created_at,
		];
	}
}
