<?php

namespace App\Http\Requests\SystemLog;

use Illuminate\Foundation\Http\FormRequest;

class ListSystemLogsRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->hasAnyRole(['admin', 'employee']) ?? false;
	}

	public function rules(): array
	{
		return [
			'search' => ['nullable', 'string', 'max:255'],
			'action' => ['nullable', 'string', 'max:100'],
			'user_type' => ['nullable', 'in:admin,employee,client,system'],
			'date_from' => ['nullable', 'date'],
			'date_to' => ['nullable', 'date'],
			'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
		];
	}
}
