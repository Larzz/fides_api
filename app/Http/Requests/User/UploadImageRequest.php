<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
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
			'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
		];
	}
}

