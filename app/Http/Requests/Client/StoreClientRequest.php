<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'primary_contact_name' => ['nullable', 'string', 'max:255'],
			'primary_contact_email' => ['nullable', 'email', 'max:255'],
			'logo' => ['nullable', 'string', 'max:2048'],
			'status' => ['required', 'in:active,inactive'],
		];
	}
}
