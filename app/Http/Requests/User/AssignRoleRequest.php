<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user()->isAdmin();
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		return [
			'role' => ['required', 'string', 'in:Admin,Manager,Staff'],
		];
	}
}

