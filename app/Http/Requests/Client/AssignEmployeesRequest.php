<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class AssignEmployeesRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'user_ids' => ['required', 'array', 'min:1'],
			'user_ids.*' => ['integer', 'exists:users,id'],
		];
	}
}
