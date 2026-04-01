<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds dedicated demo users for the Pending Requests module (2 admins, 3 employees).
 */
class PendingRequestsUserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$admins = [
			[
				'name' => 'Pending Admin One',
				'email' => 'pending-admin-1@example.com',
			],
			[
				'name' => 'Pending Admin Two',
				'email' => 'pending-admin-2@example.com',
			],
		];

		foreach ($admins as $row) {
			User::firstOrCreate(
				['email' => $row['email']],
				[
					'name' => $row['name'],
					'password' => Hash::make('password'),
					'role' => 'admin',
					'status' => 'active',
					'notes' => '',
					'image' => '',
					'resume' => '',
					'cover_letter' => '',
				]
			);
		}

		$employees = [
			[
				'name' => 'Pending Employee One',
				'email' => 'pending-employee-1@example.com',
			],
			[
				'name' => 'Pending Employee Two',
				'email' => 'pending-employee-2@example.com',
			],
			[
				'name' => 'Pending Employee Three',
				'email' => 'pending-employee-3@example.com',
			],
		];

		foreach ($employees as $row) {
			User::firstOrCreate(
				['email' => $row['email']],
				[
					'name' => $row['name'],
					'password' => Hash::make('password'),
					'role' => 'employee',
					'status' => 'active',
					'notes' => '',
					'image' => '',
					'resume' => '',
					'cover_letter' => '',
				]
			);
		}
	}
}
