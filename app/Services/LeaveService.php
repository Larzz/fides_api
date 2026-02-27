<?php

namespace App\Services;

use App\Repositories\LeaveRepository;
use App\Models\Leave;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Events\LeaveStatusChanged;

class LeaveService
{
	public function __construct(
		protected LeaveRepository $leaveRepository
	) {
	}

	/**
	 * Get all leaves
	 */
	public function getAllLeaves(int $perPage = 15): LengthAwarePaginator
	{
		return $this->leaveRepository->paginate($perPage);
	}

	/**
	 * Get leave by ID
	 */
	public function getLeaveById(int $id): Leave
	{
		return $this->leaveRepository->findOrFail($id);
	}

	/**
	 * Create leave request
	 */
	public function createLeave(array $data): Leave
	{
		return DB::transaction(function () use ($data) {
			$leave = $this->leaveRepository->create($data);

			$leave->logActivity('leave_created', 'Leave request created', [
				'leave_name' => $leave->name,
				'start_date' => $leave->start_date,
				'end_date' => $leave->end_date,
			]);

			$leave->createNotification(
				'leave_request_created',
				"New leave request created: {$leave->name}",
				$leave->user_id
			);

			return $leave;
		});
	}

	/**
	 * Update leave
	 */
	public function updateLeave(int $id, array $data): Leave
	{
		return DB::transaction(function () use ($id, $data) {
			$oldStatus = $this->leaveRepository->findOrFail($id)->status;

			$this->leaveRepository->update($id, $data);
			$leave = $this->leaveRepository->findOrFail($id);

			if (isset($data['status']) && $data['status'] !== $oldStatus) {
				event(new LeaveStatusChanged($leave, $oldStatus, $data['status']));
			}

			$leave->logActivity('leave_updated', 'Leave request updated', [
				'leave_name' => $leave->name,
			]);

			return $leave;
		});
	}

	/**
	 * Delete leave
	 */
	public function deleteLeave(int $id): bool
	{
		return DB::transaction(function () use ($id) {
			$leave = $this->leaveRepository->findOrFail($id);
			$leave->logActivity('leave_deleted', 'Leave request deleted', [
				'leave_name' => $leave->name,
			]);

			return $this->leaveRepository->delete($id);
		});
	}

	/**
	 * Approve leave
	 */
	public function approveLeave(int $id, ?string $remarks = null): Leave
	{
		return DB::transaction(function () use ($id, $remarks) {
			$leave = $this->leaveRepository->findOrFail($id);
			$oldStatus = $leave->status;

			$leave->update(['status' => 'approved']);

			if ($remarks) {
				$leave->leaveNotes()->create(['note' => $remarks]);
			}

			event(new LeaveStatusChanged($leave, $oldStatus, 'approved'));

			$leave->logActivity('leave_approved', 'Leave request approved', [
				'leave_name' => $leave->name,
				'remarks' => $remarks,
			]);

			$leave->createNotification(
				'leave_approved',
				"Leave request '{$leave->name}' has been approved",
				$leave->user_id,
				$remarks
			);

			return $leave->fresh();
		});
	}

	/**
	 * Reject leave
	 */
	public function rejectLeave(int $id, ?string $remarks = null): Leave
	{
		return DB::transaction(function () use ($id, $remarks) {
			$leave = $this->leaveRepository->findOrFail($id);
			$oldStatus = $leave->status;

			$leave->update(['status' => 'rejected']);

			if ($remarks) {
				$leave->leaveNotes()->create(['note' => $remarks]);
			}

			event(new LeaveStatusChanged($leave, $oldStatus, 'rejected'));

			$leave->logActivity('leave_rejected', 'Leave request rejected', [
				'leave_name' => $leave->name,
				'remarks' => $remarks,
			]);

			$leave->createNotification(
				'leave_rejected',
				"Leave request '{$leave->name}' has been rejected",
				$leave->user_id,
				$remarks
			);

			return $leave->fresh();
		});
	}

	/**
	 * Add note to leave
	 */
	public function addNote(int $leaveId, string $note): Leave
	{
		return DB::transaction(function () use ($leaveId, $note) {
			$leave = $this->leaveRepository->findOrFail($leaveId);
			$leave->leaveNotes()->create(['note' => $note]);

			$leave->logActivity('note_added', 'Note added to leave request', [
				'leave_name' => $leave->name,
				'note' => $note,
			]);

			return $leave->fresh();
		});
	}

	/**
	 * Get leaves with filters
	 */
	public function getLeavesWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
	{
		return $this->leaveRepository->getWithFilters($filters, $perPage);
	}

	/**
	 * Search leaves
	 */
	public function searchLeaves(string $query, int $perPage = 15): LengthAwarePaginator
	{
		return $this->leaveRepository->search($query, [], $perPage);
	}
}

