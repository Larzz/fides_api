<?php

namespace App\Http\Requests\Approval;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApprovalRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'type' => ['sometimes', 'in:leave,wfh,ticket,tool,file'],
			'title' => ['sometimes', 'string', 'max:255'],
			'description' => ['nullable', 'string'],
			'metadata' => ['nullable', 'array'],
			'status' => ['sometimes', 'in:pending,approved,rejected'],
		];
	}
}
