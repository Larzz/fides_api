<?php

namespace App\Services;

use App\Repositories\ToolRepository;
use App\Models\Tool;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Events\ToolStatusChanged;

class ToolService
{
	public function __construct(
		protected ToolRepository $toolRepository
	) {
	}

	/**
	 * Get all tools
	 */
	public function getAllTools(int $perPage = 15): LengthAwarePaginator
	{
		return $this->toolRepository->getWithRelations([
			'categoryRelation',
			'statusRelation',
			'users',
			'toolTags',
			'toolNotes',
		], $perPage);
	}

	/**
	 * Get tool by ID
	 */
	public function getToolById(int $id): Tool
	{
		return $this->toolRepository->findOrFail($id);
	}

	/**
	 * Create tool
	 */
	public function createTool(array $data): Tool
	{
		return DB::transaction(function () use ($data) {
			$tool = $this->toolRepository->create($data);

			if (isset($data['tags']) && is_array($data['tags'])) {
				foreach ($data['tags'] as $tag) {
					$tool->toolTags()->create([
						'name' => $tag,
						'description' => '',
					]);
				}
			}

			if (isset($data['user_ids']) && is_array($data['user_ids'])) {
				$tool->users()->attach($data['user_ids']);
			}

			$tool->logActivity('tool_created', 'Tool created', [
				'tool_name' => $tool->name,
			]);

			$tool->createNotification(
				'tool_created',
				"New tool '{$tool->name}' has been created",
				$tool->user_id
			);

			return $tool->fresh();
		});
	}

	/**
	 * Update tool
	 */
	public function updateTool(int $id, array $data): Tool
	{
		return DB::transaction(function () use ($id, $data) {
			$tool = $this->toolRepository->findOrFail($id);
			$oldStatus = $tool->status;

			$this->toolRepository->update($id, $data);
			$tool = $this->toolRepository->findOrFail($id);

			if (isset($data['status']) && $data['status'] !== $oldStatus) {
				event(new ToolStatusChanged($tool, $oldStatus, $data['status']));
			}

			if (isset($data['tags']) && is_array($data['tags'])) {
				$tool->toolTags()->delete();
				foreach ($data['tags'] as $tag) {
					$tool->toolTags()->create([
						'name' => $tag,
						'description' => '',
					]);
				}
			}

			if (isset($data['user_ids']) && is_array($data['user_ids'])) {
				$tool->users()->sync($data['user_ids']);
			}

			$tool->logActivity('tool_updated', 'Tool updated', [
				'tool_name' => $tool->name,
			]);

			return $tool->fresh();
		});
	}

	/**
	 * Delete tool
	 */
	public function deleteTool(int $id): bool
	{
		return DB::transaction(function () use ($id) {
			$tool = $this->toolRepository->findOrFail($id);
			$tool->logActivity('tool_deleted', 'Tool deleted', [
				'tool_name' => $tool->name,
			]);

			return $this->toolRepository->delete($id);
		});
	}

	/**
	 * Assign users to tool
	 */
	public function assignUsers(int $toolId, array $userIds): Tool
	{
		return DB::transaction(function () use ($toolId, $userIds) {
			$tool = $this->toolRepository->findOrFail($toolId);
			$tool->users()->sync($userIds);

			$tool->logActivity('users_assigned', 'Users assigned to tool', [
				'tool_name' => $tool->name,
				'user_count' => count($userIds),
			]);

			return $tool->fresh();
		});
	}

	/**
	 * Add tool note
	 */
	public function addNote(int $toolId, string $note): Tool
	{
		return DB::transaction(function () use ($toolId, $note) {
			$tool = $this->toolRepository->findOrFail($toolId);
			$tool->toolNotes()->create(['note' => $note]);

			$tool->logActivity('note_added', 'Note added to tool', [
				'tool_name' => $tool->name,
				'note' => $note,
			]);

			return $tool->fresh();
		});
	}

	/**
	 * Add tool cost
	 */
	public function addCost(int $toolId, array $costData): Tool
	{
		return DB::transaction(function () use ($toolId, $costData) {
			$tool = $this->toolRepository->findOrFail($toolId);
			$tool->toolCosts()->create($costData);

			$tool->logActivity('cost_added', 'Cost added to tool', [
				'tool_name' => $tool->name,
				'amount' => $costData['amount'] ?? 0,
			]);

			return $tool->fresh();
		});
	}

	/**
	 * Add tool billing
	 */
	public function addBilling(int $toolId, array $billingData): Tool
	{
		return DB::transaction(function () use ($toolId, $billingData) {
			$tool = $this->toolRepository->findOrFail($toolId);
			$tool->toolBillings()->create($billingData);

			$tool->logActivity('billing_added', 'Billing added to tool', [
				'tool_name' => $tool->name,
				'amount' => $billingData['amount'] ?? 0,
			]);

			return $tool->fresh();
		});
	}

	/**
	 * Get tools by category
	 */
	public function getToolsByCategory(string $category, int $perPage = 15): LengthAwarePaginator
	{
		return $this->toolRepository->getByCategory($category, $perPage);
	}

	/**
	 * Get tools by status
	 */
	public function getToolsByStatus(string $status, int $perPage = 15): LengthAwarePaginator
	{
		return $this->toolRepository->getByStatus($status, $perPage);
	}

	/**
	 * Get tools by user
	 */
	public function getToolsByUser(int $userId, int $perPage = 15): LengthAwarePaginator
	{
		return $this->toolRepository->getByUser($userId, $perPage);
	}

	/**
	 * Search tools
	 */
	public function searchTools(string $query, int $perPage = 15): LengthAwarePaginator
	{
		return $this->toolRepository->search($query, [], $perPage);
	}
}

