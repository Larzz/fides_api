<?php

namespace App\Http\Requests\PendingRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequestRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function rules(): array
	{
		return [
			'request_type_id' => [
				'required',
				'integer',
				Rule::exists('request_types', 'id')->where('is_active', true),
			],
			'details' => ['required', 'string', 'max:10000'],
			'attachments' => ['nullable', 'array', 'max:10'],
			'attachments.*' => ['file', 'max:10240'],
		];
	}
}
