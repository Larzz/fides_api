<?php

namespace App\Repositories;

use App\Models\UserContentUpload;
use Illuminate\Pagination\LengthAwarePaginator;

class ContentRepository extends BaseRepository
{
	public function __construct(UserContentUpload $model)
	{
		parent::__construct($model);
	}

	/**
	 * Get content by user
	 */
	public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
	{
		return $this->model->where('user_id', $userId)
			->with(['user', 'category', 'shares', 'downloads'])
			->paginate($perPage);
	}

	/**
	 * Get content by category
	 */
	public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
	{
		return $this->model->where('category_id', $categoryId)
			->with(['user', 'category'])
			->paginate($perPage);
	}

	/**
	 * Get content by file type
	 */
	public function getByFileType(string $fileType, int $perPage = 15): LengthAwarePaginator
	{
		return $this->model->where('file_type', $fileType)
			->with(['user', 'category'])
			->paginate($perPage);
	}

	protected function getSearchableColumns(): array
	{
		return ['file_name', 'file_type', 'file_extension'];
	}
}

