<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class IndexEmployeeRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'search' => ['nullable', 'string', 'max:255'],
			'role' => ['nullable', 'in:admin,employee,client'],
			'user_type' => ['nullable', 'in:admin,employee,client'],
			'assigned_client' => ['nullable', 'integer', 'exists:companies,id'],
			'status' => ['nullable', 'in:active,inactive,on_leave'],
			'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
		];
	}
}
