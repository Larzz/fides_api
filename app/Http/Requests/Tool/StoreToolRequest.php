<?php

namespace App\Http\Requests\Tool;

use Illuminate\Foundation\Http\FormRequest;

class StoreToolRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user()->isAdmin() || $this->user()->isManager();
	}

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'description' => ['required', 'string', 'max:1000'],
			'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
			'url' => ['nullable', 'url', 'max:255'],
			'category' => ['required', 'string', 'max:100'],
			'subcategory' => ['nullable', 'string', 'max:100'],
			'tags' => ['nullable', 'array'],
			'tags.*' => ['string', 'max:50'],
			'status' => ['nullable', 'string', 'max:50'],
			'user_ids' => ['nullable', 'array'],
			'user_ids.*' => ['integer', 'exists:users,id'],
		];
	}
}

