<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class ListFilesRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'tab' => ['nullable', 'string', 'in:all,my_uploads,client_files,team_uploads,archived'],
			'search' => ['nullable', 'string', 'max:255'],
			'category' => [
				'nullable',
				'string',
				'in:Announcement,Report,Invoice,Contract,Presentation',
			],
			'uploaded_by' => ['nullable', 'integer', 'exists:users,id'],
			'shared_company_id' => ['nullable', 'integer', 'exists:companies,id'],
			'shared_user_id' => ['nullable', 'integer', 'exists:users,id'],
			'share_all_employees' => ['nullable', 'boolean'],
			'updated_from' => ['nullable', 'date'],
			'updated_to' => ['nullable', 'date'],
			'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
			'sort_by' => ['nullable', 'string', 'in:updated_at,created_at,title,id'],
			'sort_dir' => ['nullable', 'string', 'in:asc,desc'],
		];
	}
}
