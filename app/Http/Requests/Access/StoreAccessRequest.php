<?php

namespace App\Http\Requests\Access;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccessRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->hasRole('admin') ?? false;
	}

	public function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'company_id' => ['required', 'integer', 'exists:companies,id'],
			'platform' => ['required', 'string', 'max:150'],
			'status' => ['required', 'in:active,inactive'],
			'user_ids' => ['nullable', 'array'],
			'user_ids.*' => ['integer', 'exists:users,id'],
		];
	}
}
