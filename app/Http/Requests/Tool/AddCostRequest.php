<?php

namespace App\Http\Requests\Tool;

use Illuminate\Foundation\Http\FormRequest;

class AddCostRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user()->isAdmin() || $this->user()->isStaff();
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'description' => ['nullable', 'string', 'max:500'],
			'amount' => ['required', 'numeric', 'min:0'],
			'currency' => ['required', 'string', 'max:3'],
			'date' => ['required', 'date'],
		];
	}
}

