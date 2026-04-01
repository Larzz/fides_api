<?php

namespace App\Http\Requests\Contract;

use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContractRequest extends FormRequest
{
	public function authorize(): bool
	{
		return $this->user() !== null;
	}

	public function rules(): array
	{
		return [
			'title' => ['sometimes', 'string', 'max:255'],
			'start_date' => ['sometimes', 'date'],
			'end_date' => ['sometimes', 'date'],
		];
	}

	public function withValidator($validator): void
	{
		$validator->after(function ($validator) {
			$contract = $this->route('contract');

			if (!$contract instanceof Contract) {
				return;
			}

			$start = $this->input('start_date', $contract->start_date?->toDateString());
			$end = $this->input('end_date', $contract->end_date?->toDateString());

			if ($start && $end && strtotime((string) $end) < strtotime((string) $start)) {
				$validator->errors()->add(
					'end_date',
					'The end date must be on or after the start date.'
				);
			}
		});
	}
}
