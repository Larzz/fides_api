<?php

namespace App\Http\Requests\Approval;

use Illuminate\Foundation\Http\FormRequest;

class StoreApprovalRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'type' => ['required', 'in:leave,wfh,ticket,tool,file'],
			'title' => ['required', 'string', 'max:255'],
			'description' => ['nullable', 'string'],
			'metadata' => ['nullable', 'array'],
		];
	}
}
