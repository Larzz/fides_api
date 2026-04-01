<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Http\Resources\ContractResource;
use App\Http\Responses\ApiResponse;
use App\Models\Contract;
use App\Services\ContractNotificationService;
use Illuminate\Http\JsonResponse;

class ContractController extends Controller
{
	public function update(
		UpdateContractRequest $request,
		Contract $contract
	): JsonResponse {
		$this->authorize('update', $contract);

		$previousStatus = $contract->status;
		$contract->update($request->validated());
		$contract->refresh();

		$newStatus = Contract::computeStatusForDates($contract->end_date);
		if ($contract->status !== $newStatus) {
			$contract->update(['status' => $newStatus]);
		}

		$contract->refresh();
		ContractNotificationService::notifyIfNeeded(
			$contract,
			$previousStatus,
			$contract->status
		);

		return ApiResponse::success(
			new ContractResource($contract),
			'Contract updated successfully'
		);
	}

	public function destroy(Contract $contract): JsonResponse
	{
		$this->authorize('delete', $contract);
		$contract->delete();

		return ApiResponse::success(null, 'Contract deleted successfully');
	}
}
