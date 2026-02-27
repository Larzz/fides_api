<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leave\StoreLeaveRequest;
use App\Http\Requests\Leave\UpdateLeaveRequest;
use App\Http\Requests\Leave\ApproveLeaveRequest;
use App\Http\Requests\Leave\AddNoteRequest;
use App\Http\Resources\LeaveResource;
use App\Http\Responses\ApiResponse;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
	public function __construct(
		protected LeaveService $leaveService
	) {
	}

	/**
	 * Display a listing of leaves
	 */
	public function index(Request $request): JsonResponse
	{
		$filters = $request->only(['start_date', 'end_date', 'status', 'user_id', 'type']);
		$perPage = $request->get('per_page', 15);

		if (!empty($filters)) {
			$leaves = $this->leaveService->getLeavesWithFilters($filters, $perPage);
		} else {
			$leaves = $this->leaveService->getAllLeaves($perPage);
		}

		return ApiResponse::paginated(
			LeaveResource::collection($leaves),
			'Leaves retrieved successfully'
		);
	}

	/**
	 * Store a newly created leave request
	 */
	public function store(StoreLeaveRequest $request): JsonResponse
	{
		$data = $request->validated();
		$data['user_id'] = $request->user()->id;
		$data['status'] = 'pending';

		$leave = $this->leaveService->createLeave($data);

		return ApiResponse::created(
			new LeaveResource($leave->load(['user', 'statusRelation'])),
			'Leave request created successfully'
		);
	}

	/**
	 * Display the specified leave
	 */
	public function show(int $id): JsonResponse
	{
		$leave = $this->leaveService->getLeaveById($id);

		return ApiResponse::success(
			new LeaveResource($leave->load(['user', 'statusRelation', 'leaveNotes'])),
			'Leave retrieved successfully'
		);
	}

	/**
	 * Update the specified leave
	 */
	public function update(UpdateLeaveRequest $request, int $id): JsonResponse
	{
		$leave = $this->leaveService->updateLeave($id, $request->validated());

		return ApiResponse::success(
			new LeaveResource($leave->load(['user', 'statusRelation'])),
			'Leave updated successfully'
		);
	}

	/**
	 * Remove the specified leave
	 */
	public function destroy(int $id): JsonResponse
	{
		$this->leaveService->deleteLeave($id);

		return ApiResponse::success(
			null,
			'Leave deleted successfully'
		);
	}

	/**
	 * Approve leave request
	 */
	public function approve(ApproveLeaveRequest $request, int $id): JsonResponse
	{
		$leave = $this->leaveService->approveLeave($id, $request->input('remarks'));

		return ApiResponse::success(
			new LeaveResource($leave->load(['user', 'statusRelation', 'leaveNotes'])),
			'Leave approved successfully'
		);
	}

	/**
	 * Reject leave request
	 */
	public function reject(ApproveLeaveRequest $request, int $id): JsonResponse
	{
		$leave = $this->leaveService->rejectLeave($id, $request->input('remarks'));

		return ApiResponse::success(
			new LeaveResource($leave->load(['user', 'statusRelation', 'leaveNotes'])),
			'Leave rejected successfully'
		);
	}

	/**
	 * Add note to leave
	 */
	public function addNote(AddNoteRequest $request, int $id): JsonResponse
	{
		$leave = $this->leaveService->addNote($id, $request->validated()['note']);

		return ApiResponse::success(
			new LeaveResource($leave->load('leaveNotes')),
			'Note added successfully'
		);
	}

	/**
	 * Search leaves
	 */
	public function search(Request $request): JsonResponse
	{
		$request->validate([
			'query' => ['required', 'string', 'min:2'],
		]);

		$perPage = $request->get('per_page', 15);
		$leaves = $this->leaveService->searchLeaves($request->input('query'), $perPage);

		return ApiResponse::paginated(
			LeaveResource::collection($leaves),
			'Leaves found successfully'
		);
	}
}

