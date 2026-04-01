<?php

namespace Database\Seeders;

use App\Models\RequestType;
use Illuminate\Database\Seeder;

class RequestTypeSeeder extends Seeder
{
	/**
	 * Seed the canonical request types shown in the product UI.
	 */
	public function run(): void
	{
		$types = [
			'Leave Request',
			'Ticket Allowance',
			'WFH Request',
			'File Submission',
			'Tool Request',
		];

		foreach ($types as $name) {
			RequestType::firstOrCreate(
				['name' => $name],
				['is_active' => true]
			);
		}
	}
}
