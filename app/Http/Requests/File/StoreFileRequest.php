<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'title' => ['required', 'string', 'max:255'],
			'category' => [
				'nullable',
				'string',
				'in:Announcement,Report,Invoice,Contract,Presentation',
			],
			'notify_stakeholders' => ['sometimes', 'boolean'],
			'file' => ['required', 'file', 'max:10240'],
		];
	}
}
