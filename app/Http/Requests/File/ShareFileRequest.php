<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class ShareFileRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'share_all_employees' => ['sometimes', 'boolean'],
			'shared_with_user_id' => ['nullable', 'integer', 'exists:users,id'],
			'shared_with_company_id' => ['nullable', 'integer', 'exists:companies,id'],
			'notifications_enabled' => ['sometimes', 'boolean'],
		];
	}

	public function withValidator($validator): void
	{
		$validator->after(function ($validator) {
			$all = filter_var(
				$this->input('share_all_employees', false),
				FILTER_VALIDATE_BOOLEAN
			);
			$userId = $this->input('shared_with_user_id');
			$companyId = $this->input('shared_with_company_id');

			if (!$all && !$userId && !$companyId) {
				$validator->errors()->add(
					'shared_with_user_id',
					'Provide share_all_employees, shared_with_user_id, or shared_with_company_id.'
				);
			}
		});
	}
}
