<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
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
			'name' => ['required', 'string', 'max:255'],
			'start_date' => ['required', 'date', 'after_or_equal:today'],
			'end_date' => ['required', 'date', 'after_or_equal:start_date'],
			'type' => ['required', 'string', 'in:vacation,sick,personal,other'],
			'reason' => ['required', 'string', 'max:500'],
			'description' => ['nullable', 'string', 'max:1000'],
			'notes' => ['nullable', 'string', 'max:1000'],
		];
	}
}

