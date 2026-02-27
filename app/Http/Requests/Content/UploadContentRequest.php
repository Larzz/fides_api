<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;

class UploadContentRequest extends FormRequest
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
			'file' => ['required', 'file', 'max:10240'],
			'category_id' => ['nullable', 'integer', 'exists:user_content_categories,id'],
		];
	}
}

