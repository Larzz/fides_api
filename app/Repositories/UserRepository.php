<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository
{
	public function __construct(User $model)
	{
		parent::__construct($model);
	}

	/**
	 * Get users with relationships
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
	 * Get users by role
	 */
	public function getByRole(string $role, int $perPage = 15): LengthAwarePaginator
	{
		return $this->model->where('role', $role)->paginate($perPage);
	}

	/**
	 * Get users by status
	 */
	public function getByStatus(string $status, int $perPage = 15): LengthAwarePaginator
	{
		return $this->model->where('status', $status)->paginate($perPage);
	}

	protected function getSearchableColumns(): array
	{
		return ['name', 'email', 'phone'];
	}
}

