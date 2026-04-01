<?php

namespace App\Filters;

use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;

/**
 * Manual filter/query helper for {@see ServiceRequest} listings (Spatie-free).
 */
class RequestFilter
{
	/**
	 * Apply validated filter/sort options to the query.
	 *
	 * @param  Builder<ServiceRequest>  $query
	 * @return Builder<ServiceRequest>
	 */
	public static function apply(Builder $query, array $input): Builder
	{
		if (!empty($input['search'])) {
			$search = $input['search'];
			$query->whereHas('user', function (Builder $userQuery) use ($search) {
				$userQuery->where('name', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%");
			});
		}

		if (!empty($input['request_type_id'])) {
			$query->where('request_type_id', (int) $input['request_type_id']);
		}

		if (!empty($input['user_type'])) {
			$query->whereHas('user', function (Builder $userQuery) use ($input) {
				$userQuery->where(
					'role',
					strtolower((string) $input['user_type'])
				);
			});
		}

		if (!empty($input['status'])) {
			$query->where('status', (string) $input['status']);
		}

		$sortBy = $input['sort_by'] ?? 'submitted_at';
		$sortDir = strtolower((string) ($input['sort_dir'] ?? 'desc')) === 'asc'
			? 'asc'
			: 'desc';

		$allowedSort = ['submitted_at', 'created_at', 'status', 'id'];
		if (!in_array($sortBy, $allowedSort, true)) {
			$sortBy = 'submitted_at';
		}

		$query->orderBy($sortBy, $sortDir);

		return $query;
	}
}
