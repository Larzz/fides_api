<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user()->isAdmin() || $this->user()->isManager();
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
			'role' => ['required', 'string', 'in:Admin,Manager,Staff'],
			'status' => ['nullable', 'string'],
			'phone' => ['nullable', 'string', 'max:20'],
			'address' => ['nullable', 'string', 'max:255'],
			'city' => ['nullable', 'string', 'max:100'],
			'state' => ['nullable', 'string', 'max:100'],
			'zip' => ['nullable', 'string', 'max:20'],
			'country' => ['nullable', 'string', 'max:100'],
		];
	}
}

