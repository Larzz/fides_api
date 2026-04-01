<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->hasAnyRole(['admin', 'employee']) ?? false;
	}

	public function rules(): array
	{
		return [
			'user_id' => ['required', 'integer', 'exists:users,id'],
			'title' => ['required', 'string', 'max:255'],
			'message' => ['required', 'string'],
			'type' => ['required', 'string', 'max:100'],
		];
	}
}
