<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeManagementSeeder extends Seeder
{
	public function run(): void
	{
		$clients = collect([
			'Acme Corp',
			'Globex Ltd',
			'Initech LLC',
		])->map(fn ($name) => Company::firstOrCreate(['name' => $name]));

		$employees = [
			[
				'name' => 'Emma Admin',
				'email' => 'emma.admin@example.com',
				'role' => 'admin',
				'job_title' => 'Operations Manager',
				'status' => 'active',
			],
			[
				'name' => 'Ethan Employee',
				'email' => 'ethan.employee@example.com',
				'role' => 'employee',
				'job_title' => 'Software Engineer',
				'status' => 'active',
			],
			[
				'name' => 'Olivia Employee',
				'email' => 'olivia.employee@example.com',
				'role' => 'employee',
				'job_title' => 'QA Analyst',
				'status' => 'on_leave',
			],
		];

		foreach ($employees as $employeeData) {
			$employee = User::firstOrCreate(
				['email' => $employeeData['email']],
				[
					'name' => $employeeData['name'],
					'password' => Hash::make('password'),
					'role' => $employeeData['role'],
					'job_title' => $employeeData['job_title'],
					'status' => $employeeData['status'],
					'notes' => '',
					'image' => '',
					'resume' => '',
					'cover_letter' => '',
				]
			);

			if ($employee->role === 'employee') {
				$assignedIds = $clients->random(2)->pluck('id')->all();
				$syncData = [];
				foreach ($assignedIds as $companyId) {
					$syncData[$companyId] = ['assigned_at' => now()];
				}
				$employee->companies()->syncWithoutDetaching($syncData);
			}
		}
	}
}
