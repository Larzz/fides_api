<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user()->isAdmin() || $this->user()->isManager() || $this->user()->id === (int) $this->route('user');
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		$userId = $this->route('user');

		return [
			'name' => ['sometimes', 'required', 'string', 'max:255'],
			'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
			'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
			'role' => ['sometimes', 'required', 'string', 'in:Admin,Manager,Staff'],
			'status' => ['sometimes', 'nullable', 'string'],
			'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
			'address' => ['sometimes', 'nullable', 'string', 'max:255'],
			'city' => ['sometimes', 'nullable', 'string', 'max:100'],
			'state' => ['sometimes', 'nullable', 'string', 'max:100'],
			'zip' => ['sometimes', 'nullable', 'string', 'max:20'],
			'country' => ['sometimes', 'nullable', 'string', 'max:100'],
		];
	}
}

