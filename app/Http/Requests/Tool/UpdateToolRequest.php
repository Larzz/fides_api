<?php

namespace App\Http\Requests\Tool;

use Illuminate\Foundation\Http\FormRequest;

class UpdateToolRequest extends FormRequest
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
			'name' => ['sometimes', 'required', 'string', 'max:255'],
			'description' => ['sometimes', 'required', 'string', 'max:1000'],
			'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
			'url' => ['sometimes', 'nullable', 'url', 'max:255'],
			'category' => ['sometimes', 'required', 'string', 'max:100'],
			'subcategory' => ['sometimes', 'nullable', 'string', 'max:100'],
			'tags' => ['sometimes', 'nullable', 'array'],
			'tags.*' => ['string', 'max:50'],
			'status' => ['sometimes', 'nullable', 'string', 'max:50'],
			'user_ids' => ['sometimes', 'nullable', 'array'],
			'user_ids.*' => ['integer', 'exists:users,id'],
		];
	}
}

