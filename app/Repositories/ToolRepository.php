<?php

namespace App\Repositories;

use App\Models\Tool;
use Illuminate\Pagination\LengthAwarePaginator;

class ToolRepository extends BaseRepository
{
	public function __construct(Tool $model)
	{
		parent::__construct($model);
	}

	/**
	 * Get tools with relationships
	 */
	public function getWithRelations(array $relations = [], int $perPage = 15): LengthAwarePaginator
	{
		$query = $this->model->newQuery();

		if (!empty($relations)) {
			$query->with($relations);
		}

		return $query->paginate($perPage);
	}

	/**
	 * Get tools by category
	 */
	public function getByCategory(string $category, int $perPage = 15): LengthAwarePaginator
	{
		return $this->model->where('category', $category)
			->with(['categoryRelation', 'statusRelation', 'users', 'toolTags', 'toolNotes'])
			->paginate($perPage);
	}

	/**
	 * Get tools by status
	 */
	public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
	{
		return $this->model->where('status', $status)
			->with(['categoryRelation', 'statusRelation', 'users'])
			->paginate($perPage);
	}

	/**
	 * Get tools by user
	 */
	public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
	{
		return $this->model->whereHas('users', function ($query) use ($userId) {
			$query->where('users.id', $userId);
		})->with(['categoryRelation', 'statusRelation', 'users'])->paginate($perPage);
	}

	protected function getSearchableColumns(): array
	{
		return ['name', 'description', 'category', 'subcategory'];
	}
}

