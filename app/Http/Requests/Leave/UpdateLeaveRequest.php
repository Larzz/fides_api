<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaveRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		return [
			'name' => ['sometimes', 'required', 'string', 'max:255'],
			'start_date' => ['sometimes', 'required', 'date'],
			'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
			'status' => ['sometimes', 'required', 'string', 'in:pending,approved,rejected,cancelled'],
			'type' => ['sometimes', 'required', 'string', 'in:vacation,sick,personal,other'],
			'reason' => ['sometimes', 'required', 'string', 'max:500'],
			'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
			'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
		];
	}
}

