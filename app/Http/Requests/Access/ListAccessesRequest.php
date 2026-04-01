<?php

namespace App\Http\Requests\Access;

use Illuminate\Foundation\Http\FormRequest;

class ListAccessesRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->hasRole('admin') ?? false;
	}

	public function rules(): array
	{
		return [
			'search' => ['nullable', 'string', 'max:255'],
			'company_id' => ['nullable', 'integer', 'exists:companies,id'],
			'platform' => ['nullable', 'string', 'max:100'],
			'status' => ['nullable', 'in:active,inactive'],
			'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
		];
	}
}
