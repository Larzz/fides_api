<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
	protected Model $model;

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	/**
	 * Get all records
	 */
	public function all(array $columns = ['*']): Collection
	{
		return $this->model->select($columns)->get();
	}

	/**
	 * Get paginated records
	 */
	public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
	{
		return $this->model->select($columns)->paginate($perPage);
	}

	/**
	 * Find record by ID
	 */
	public function find(int $id, array $columns = ['*']): ?Model
	{
		return $this->model->select($columns)->find($id);
	}

	/**
	 * Find or fail record by ID
	 */
	public function findOrFail(int $id, array $columns = ['*']): Model
	{
		return $this->model->select($columns)->findOrFail($id);
	}

	/**
	 * Create a new record
	 */
	public function create(array $data): Model
	{
		return $this->model->create($data);
	}

	/**
	 * Update a record
	 */
	public function update(int $id, array $data): bool
	{
		$record = $this->findOrFail($id);
		return $record->update($data);
	}

	/**
	 * Delete a record
	 */
	public function delete(int $id): bool
	{
		$record = $this->findOrFail($id);
		return $record->delete();
	}

	/**
	 * Get records with filters
	 */
	public function getWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
	{
		$query = $this->model->newQuery();

		foreach ($filters as $key => $value) {
			if ($value !== null && $value !== '') {
				$query->where($key, $value);
			}
		}

		return $query->paginate($perPage);
	}

	/**
	 * Search records
	 */
	public function search(string $query, array $columns = [], int $perPage = 15): LengthAwarePaginator
	{
		$searchQuery = $this->model->newQuery();

		if (empty($columns)) {
			$columns = $this->getSearchableColumns();
		}

		$searchQuery->where(function ($q) use ($query, $columns) {
			foreach ($columns as $column) {
				$q->orWhere($column, 'like', "%{$query}%");
			}
		});

		return $searchQuery->paginate($perPage);
	}

	/**
	 * Get searchable columns (override in child classes)
	 */
	protected function getSearchableColumns(): array
	{
		return ['name', 'description'];
	}
}

