<?php

namespace App\Http\Requests\Access;

use Illuminate\Foundation\Http\FormRequest;

class AssignAccessUsersRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->hasRole('admin') ?? false;
	}

	public function rules(): array
	{
		return [
			'user_ids' => ['required', 'array', 'min:1'],
			'user_ids.*' => ['integer', 'exists:users,id'],
			'mode' => ['nullable', 'in:sync,attach'],
		];
	}
}
