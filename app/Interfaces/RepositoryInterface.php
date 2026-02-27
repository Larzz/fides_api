<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
	/**
	 * Get all records
	 */
	public function all(array $columns = ['*']): Collection;

	/**
	 * Get paginated records
	 */
	public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

	/**
	 * Find record by ID
	 */
	public function find(int $id, array $columns = ['*']): ?Model;

	/**
	 * Find or fail record by ID
	 */
	public function findOrFail(int $id, array $columns = ['*']): Model;

	/**
	 * Create a new record
	 */
	public function create(array $data): Model;

	/**
	 * Update a record
	 */
	public function update(int $id, array $data): bool;

	/**
	 * Delete a record
	 */
	public function delete(int $id): bool;

	/**
	 * Get records with filters
	 */
	public function getWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator;

	/**
	 * Search records
	 */
	public function search(string $query, array $columns = [], int $perPage = 15): LengthAwarePaginator;
}

