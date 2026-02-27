<?php

namespace App\Repositories;

use App\Models\Leave;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaveRepository extends BaseRepository
{
	public function __construct(Leave $model)
	{
		parent::__construct($model);
	}

	/**
	 * Get leaves with filters
	 */
	public function getWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
	{
		$query = $this->model->newQuery();

		if (isset($filters['start_date']) || isset($filters['end_date'])) {
			$query->dateRange($filters['start_date'] ?? null, $filters['end_date'] ?? null);
		}

		if (isset($filters['status'])) {
			$query->byStatus($filters['status']);
		}

		if (isset($filters['user_id'])) {
			$query->byUser($filters['user_id']);
		}

		if (isset($filters['type'])) {
			$query->where('type', $filters['type']);
		}

		$query->with(['user', 'statusRelation', 'leaveNotes']);

		return $query->paginate($perPage);
	}

	protected function getSearchableColumns(): array
	{
		return ['name', 'reason', 'description', 'type'];
	}
}

