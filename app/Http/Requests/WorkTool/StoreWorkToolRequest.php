<?php

namespace App\Http\Requests\WorkTool;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkToolRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user()?->hasRole('admin') ?? false;
	}

	public function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'category' => ['required', 'string', 'max:100'],
			'billing_type' => ['required', 'in:monthly,annual,free'],
			'cost' => ['required', 'numeric', 'min:0'],
			'currency' => ['required', 'string', 'size:3'],
			'renewal_date' => ['nullable', 'date'],
			'status' => ['required', 'in:active,inactive'],
			'notes' => ['nullable', 'string', 'max:5000'],
		];
	}
}
