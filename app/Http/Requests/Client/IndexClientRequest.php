<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class IndexClientRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'search' => ['nullable', 'string', 'max:255'],
			'status' => ['nullable', 'in:active,inactive'],
			'assigned_employees' => ['nullable', 'integer', 'exists:users,id'],
			'contract_status' => ['nullable', 'in:active,expiring,expired'],
			'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
		];
	}
}
