<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Services\ContractNotificationService;
use Illuminate\Console\Command;

class CheckContractStatuses extends Command
{
	protected $signature = 'contracts:check-status';

	protected $description = 'Recompute contract statuses (active / expiring / expired) and notify stakeholders';

	public function handle(): int
	{
		$updated = 0;

		Contract::query()->orderBy('id')->chunkById(100, function ($contracts) use (&$updated) {
			foreach ($contracts as $contract) {
				$previous = $contract->status;
				$computed = Contract::computeStatusForDates($contract->end_date);

				if ($previous !== $computed) {
					$contract->forceFill(['status' => $computed])->save();
					ContractNotificationService::notifyIfNeeded(
						$contract->fresh(),
						$previous,
						$computed
					);
					$updated++;
				}
			}
		});

		$this->info("Checked contracts; {$updated} status updates applied.");

		return self::SUCCESS;
	}
}
