<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contract;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ClientContractSeeder extends Seeder
{
	public function run(): void
	{
		$companyA = Company::firstOrCreate(
			['name' => 'Helios Healthcare'],
			[
				'primary_contact_name' => 'Alex Morgan',
				'primary_contact_email' => 'alex@helios.example',
				'status' => 'active',
			]
		);

		$companyB = Company::firstOrCreate(
			['name' => 'Summit Retail Group'],
			[
				'primary_contact_name' => 'Jordan Lee',
				'primary_contact_email' => 'jordan@summit.example',
				'status' => 'inactive',
			]
		);

		$employee = User::where('email', 'ethan.employee@example.com')->first();
		if ($employee) {
			$companyA->users()->syncWithoutDetaching([
				$employee->id => ['assigned_at' => now()],
			]);
		}

		$endsSoon = Carbon::now()->addDays(14);
		$contractExpiring = Contract::firstOrCreate(
			[
				'company_id' => $companyA->id,
				'title' => 'MSA 2025',
			],
			[
				'start_date' => Carbon::now()->subMonths(6)->toDateString(),
				'end_date' => $endsSoon->toDateString(),
				'status' => Contract::computeStatusForDates($endsSoon),
			]
		);
		$contractExpiring->update([
			'status' => Contract::computeStatusForDates($contractExpiring->end_date),
		]);

		$ended = Carbon::now()->subDays(5);
		$contractExpired = Contract::firstOrCreate(
			[
				'company_id' => $companyA->id,
				'title' => 'Legacy SLA',
			],
			[
				'start_date' => Carbon::now()->subYear()->toDateString(),
				'end_date' => $ended->toDateString(),
				'status' => Contract::computeStatusForDates($ended),
			]
		);
		$contractExpired->update([
			'status' => Contract::computeStatusForDates($contractExpired->end_date),
		]);

		$futureEnd = Carbon::now()->addMonths(6);
		$contractActive = Contract::firstOrCreate(
			[
				'company_id' => $companyB->id,
				'title' => 'Pilot Agreement',
			],
			[
				'start_date' => Carbon::now()->subMonth()->toDateString(),
				'end_date' => $futureEnd->toDateString(),
				'status' => Contract::computeStatusForDates($futureEnd),
			]
		);
		$contractActive->update([
			'status' => Contract::computeStatusForDates($contractActive->end_date),
		]);
	}
}
