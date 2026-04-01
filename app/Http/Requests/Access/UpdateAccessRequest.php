<?php

namespace App\Http\Requests\Access;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccessRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->hasRole('admin') ?? false;
	}

	public function rules(): array
	{
		return [
			'name' => ['sometimes', 'string', 'max:255'],
			'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
			'platform' => ['sometimes', 'string', 'max:150'],
			'status' => ['sometimes', 'in:active,inactive'],
		];
	}
}
