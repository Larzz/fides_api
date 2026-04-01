<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class AssignClientsRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'company_ids' => ['required', 'array', 'min:1'],
			'company_ids.*' => ['integer', 'exists:companies,id'],
		];
	}
}
