<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Approval;
use App\Models\Company;
use App\Models\DashboardFile;
use App\Models\DashboardNotification;
use App\Models\FileShare;
use App\Models\User;
use Illuminate\Database\Seeder;

class DashboardModuleSeeder extends Seeder
{
	public function run(): void
	{
		$admin = User::firstOrCreate(
			['email' => 'admin@dashboard.local'],
			[
				'name' => 'Admin User',
				'password' => bcrypt('password'),
				'role' => 'admin',
				'status' => 'active',
				'notes' => '',
				'image' => '',
				'resume' => '',
				'cover_letter' => '',
			]
		);

		$employee = User::firstOrCreate(
			['email' => 'employee@dashboard.local'],
			[
				'name' => 'Employee User',
				'password' => bcrypt('password'),
				'role' => 'employee',
				'status' => 'active',
				'notes' => '',
				'image' => '',
				'resume' => '',
				'cover_letter' => '',
			]
		);

		$client = User::firstOrCreate(
			['email' => 'client@dashboard.local'],
			[
				'name' => 'Client User',
				'password' => bcrypt('password'),
				'role' => 'client',
				'status' => 'active',
				'notes' => '',
				'image' => '',
				'resume' => '',
				'cover_letter' => '',
			]
		);

		$company = Company::firstOrCreate(['name' => 'Acme Client Co']);
		$company->users()->syncWithoutDetaching([$client->id]);

		$approval = Approval::firstOrCreate(
			[
				'user_id' => $employee->id,
				'title' => 'WFH Request - Friday',
			],
			[
				'type' => 'wfh',
				'description' => 'Requesting work from home due to travel time.',
				'metadata' => ['date' => now()->toDateString()],
				'status' => 'pending',
			]
		);

		$file = DashboardFile::firstOrCreate(
			['title' => 'Client Contract'],
			[
				'file_path' => 'dashboard-files/sample-contract.pdf',
				'uploaded_by' => $admin->id,
			]
		);

		FileShare::firstOrCreate([
			'file_id' => $file->id,
			'shared_with_company_id' => $company->id,
		], [
			'notifications_enabled' => true,
			'created_at' => now(),
		]);

		DashboardNotification::firstOrCreate([
			'user_id' => $employee->id,
			'title' => 'Approval Submitted',
		], [
			'message' => 'Your request has been submitted successfully.',
			'type' => 'approval_submitted',
			'created_at' => now(),
		]);

		Activity::firstOrCreate([
			'user_id' => $admin->id,
			'action' => 'logged_in',
			'description' => 'Admin user logged in.',
		], [
			'created_at' => now(),
		]);

		Activity::firstOrCreate([
			'user_id' => $admin->id,
			'action' => 'file_uploaded',
			'description' => 'Uploaded Client Contract.',
		], [
			'created_at' => now(),
		]);
	}
}
