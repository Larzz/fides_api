<?php

namespace Database\Seeders;

use App\Enums\RequestWorkflowStatus;
use App\Models\RequestType;
use App\Models\ServiceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeds a handful of demo {@see ServiceRequest} rows for local testing.
 */
class PendingRequestDemoSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$types = RequestType::query()->pluck('id')->all();
		$employees = User::query()->where('role', 'employee')->pluck('id')->all();
		$admins = User::query()->where('role', 'admin')->pluck('id')->all();

		if ($types === [] || $employees === []) {
			$this->command?->warn('Skipping PendingRequestDemoSeeder (types or employees missing).');

			return;
		}

		$statusCycle = [
			RequestWorkflowStatus::Pending,
			RequestWorkflowStatus::Reviewed,
			RequestWorkflowStatus::Reimbursed,
			RequestWorkflowStatus::Rejected,
			RequestWorkflowStatus::ActionRequired,
		];

		$samples = [
			'Annual · 18–20 Mar (3 days)',
			'Invoice uploaded (AED 2,532)',
			'WFH · 2 days (home office)',
			'Tool: laptop dock reimbursement',
			'Leave: sick day 12 Apr',
			'Parking tickets batch (3)',
			'Client visit expenses · 450 AED',
		];

		for ($i = 0; $i < 20; $i++) {
			$typeId = $types[$i % count($types)];
			$userId = $employees[$i % count($employees)];
			$status = $statusCycle[$i % count($statusCycle)];
			$submitted = Carbon::now()->subDays($i + 1)->setTime(10, 15, 0);

			$data = [
				'request_type_id' => $typeId,
				'user_id' => $userId,
				'details' => $samples[$i % count($samples)]." (#{$i})",
				'status' => $status,
				'submitted_at' => $submitted,
				'notes' => null,
				'reviewed_by' => null,
				'reviewed_at' => null,
			];

			if ($status !== RequestWorkflowStatus::Pending && $admins !== []) {
				$data['reviewed_by'] = $admins[$i % count($admins)];
				$data['reviewed_at'] = $submitted->copy()->addHours(3);
				$data['notes'] = 'Seeded review note.';
			}

			ServiceRequest::query()->create($data);
		}
	}
}
