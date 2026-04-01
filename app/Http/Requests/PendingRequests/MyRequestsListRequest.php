<?php

namespace App\Http\Requests\PendingRequests;

use Illuminate\Foundation\Http\FormRequest;

class MyRequestsListRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function rules(): array
	{
		return [
			'search' => ['nullable', 'string', 'max:255'],
			'request_type_id' => ['nullable', 'integer', 'exists:request_types,id'],
			'status' => [
				'nullable',
				'string',
				'in:pending,reviewed,reimbursed,rejected,action_required',
			],
			'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
			'page' => ['nullable', 'integer', 'min:1'],
			'sort_by' => [
				'nullable',
				'string',
				'in:submitted_at,created_at,status,id',
			],
			'sort_dir' => ['nullable', 'string', 'in:asc,desc'],
		];
	}
}
