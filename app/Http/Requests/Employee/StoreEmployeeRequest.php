<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', 'max:255', 'unique:users,email'],
			'password' => ['required', 'string', 'min:8'],
			'role' => ['required', 'in:admin,employee,client'],
			'job_title' => ['nullable', 'string', 'max:255'],
			'status' => ['required', 'in:active,inactive,on_leave'],
			'avatar' => ['nullable', 'string', 'max:2048'],
		];
	}
}
