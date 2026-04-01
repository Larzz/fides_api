<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		$employeeId = $this->route('employee')?->id ?? $this->route('employee');

		return [
			'name' => ['sometimes', 'string', 'max:255'],
			'email' => [
				'sometimes',
				'email',
				'max:255',
				Rule::unique('users', 'email')->ignore($employeeId),
			],
			'password' => ['sometimes', 'string', 'min:8'],
			'role' => ['sometimes', 'in:admin,employee,client'],
			'job_title' => ['nullable', 'string', 'max:255'],
			'status' => ['sometimes', 'in:active,inactive,on_leave'],
			'avatar' => ['nullable', 'string', 'max:2048'],
		];
	}
}
