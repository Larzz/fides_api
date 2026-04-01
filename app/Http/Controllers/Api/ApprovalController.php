<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Approval\ApprovalDecisionRequest;
use App\Http\Requests\Approval\StoreApprovalRequest;
use App\Http\Requests\Approval\UpdateApprovalRequest;
use App\Http\Resources\ApprovalResource;
use App\Http\Responses\ApiResponse;
use App\Models\Approval;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
	public function index(Request $request): JsonResponse
	{
		$approvals = Approval::query()
			->with(['user', 'approver'])
			->latest()
			->paginate((int) $request->input('per_page', 15));

		$approvals->through(fn ($approval) => new ApprovalResource($approval));

		return ApiResponse::paginated(
			$approvals,
			'Approvals retrieved successfully'
		);
	}

	public function store(StoreApprovalRequest $request): JsonResponse
	{
		$approval = Approval::create([
			...$request->validated(),
			'user_id' => $request->user()->id,
			'status' => 'pending',
		]);

		Activity::create([
			'user_id' => $request->user()->id,
			'action' => 'approval_submitted',
			'description' => 'Approval request submitted: '.$approval->title,
		]);

		return ApiResponse::created(
			new ApprovalResource($approval->load(['user', 'approver'])),
			'Approval created successfully'
		);
	}

	public function show(Approval $approval): JsonResponse
	{
		return ApiResponse::success(
			new ApprovalResource($approval->load(['user', 'approver'])),
			'Approval retrieved successfully'
		);
	}

	public function update(
		UpdateApprovalRequest $request,
		Approval $approval
	): JsonResponse {
		$this->authorizeOwnerOrPrivileged($request, $approval);

		$approval->update($request->validated());

		return ApiResponse::success(
			new ApprovalResource($approval->load(['user', 'approver'])),
			'Approval updated successfully'
		);
	}

	public function approve(
		ApprovalDecisionRequest $request,
		Approval $approval
	): JsonResponse {
		$approval->update([
			'status' => 'approved',
			'approved_by' => $request->user()->id,
			'approved_at' => now(),
		]);

		Activity::create([
			'user_id' => $request->user()->id,
			'action' => 'approval_approved',
			'description' => 'Approval approved: '.$approval->title,
		]);

		return ApiResponse::success(
			new ApprovalResource($approval->load(['user', 'approver'])),
			'Approval approved successfully'
		);
	}

	public function reject(
		ApprovalDecisionRequest $request,
		Approval $approval
	): JsonResponse {
		$approval->update([
			'status' => 'rejected',
			'approved_by' => $request->user()->id,
			'approved_at' => now(),
		]);

		Activity::create([
			'user_id' => $request->user()->id,
			'action' => 'approval_rejected',
			'description' => 'Approval rejected: '.$approval->title,
		]);

		return ApiResponse::success(
			new ApprovalResource($approval->load(['user', 'approver'])),
			'Approval rejected successfully'
		);
	}

	private function authorizeOwnerOrPrivileged(
		Request $request,
		Approval $approval
	): void {
		$isOwner = $approval->user_id === $request->user()->id;
		$isPrivileged = $request->user()->hasAnyRole(['admin', 'employee']);

		abort_unless($isOwner || $isPrivileged, 403, 'Insufficient permissions');
	}
}
