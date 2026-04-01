<?php

namespace App\Http\Requests\WorkTool;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkToolRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->hasRole('admin') ?? false;
	}

	public function rules(): array
	{
		return [
			'name' => ['sometimes', 'string', 'max:255'],
			'category' => ['sometimes', 'string', 'max:100'],
			'billing_type' => ['sometimes', 'in:monthly,annual,free'],
			'cost' => ['sometimes', 'numeric', 'min:0'],
			'currency' => ['sometimes', 'string', 'size:3'],
			'renewal_date' => ['nullable', 'date'],
			'status' => ['sometimes', 'in:active,inactive'],
			'notes' => ['nullable', 'string', 'max:5000'],
		];
	}
}
