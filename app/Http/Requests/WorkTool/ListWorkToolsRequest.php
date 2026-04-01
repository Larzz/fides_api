<?php

namespace App\Http\Requests\WorkTool;

use Illuminate\Foundation\Http\FormRequest;

class ListWorkToolsRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'search' => ['nullable', 'string', 'max:255'],
			'category' => ['nullable', 'string', 'max:100'],
			'billing_type' => ['nullable', 'in:monthly,annual,free'],
			'status' => ['nullable', 'in:active,inactive'],
			'renewal_from' => ['nullable', 'date'],
			'renewal_to' => ['nullable', 'date'],
			'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
			'sort_by' => ['nullable', 'string', 'max:50'],
			'sort_order' => ['nullable', 'in:asc,desc'],
		];
	}
}
