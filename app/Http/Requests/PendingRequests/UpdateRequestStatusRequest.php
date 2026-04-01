<?php

namespace App\Http\Requests\PendingRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestStatusRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user() !== null && $this->user()->hasRole('admin');
	}

	/**
	 * @return array<string, mixed>
	 */
	public function rules(): array
	{
		return [
			'status' => [
				'required',
				'string',
				'in:pending,reviewed,reimbursed,rejected,action_required',
			],
			'notes' => ['nullable', 'string', 'max:10000'],
		];
	}
}
