<?php

namespace App\Filters;

use App\Models\WorkTool;
use Illuminate\Database\Eloquent\Builder;

class WorkToolFilter
{
	/**
	 * @param  Builder<WorkTool>  $query
	 * @return Builder<WorkTool>
	 */
	public static function apply(Builder $query, array $input): Builder
	{
		if (!empty($input['search'])) {
			$s = $input['search'];
			$query->where('name', 'like', "%{$s}%");
		}

		if (!empty($input['category'])) {
			$query->where('category', $input['category']);
		}

		if (!empty($input['billing_type'])) {
			$query->where('billing_type', $input['billing_type']);
		}

		if (!empty($input['status'])) {
			$query->where('status', $input['status']);
		}

		if (!empty($input['renewal_from'])) {
			$query->whereDate('renewal_date', '>=', $input['renewal_from']);
		}

		if (!empty($input['renewal_to'])) {
			$query->whereDate('renewal_date', '<=', $input['renewal_to']);
		}

		$sortBy = $input['sort_by'] ?? 'name';
		$dir = strtolower((string) ($input['sort_order'] ?? 'asc')) === 'desc'
			? 'desc'
			: 'asc';

		$allowed = ['name', 'category', 'billing_type', 'cost', 'renewal_date', 'status', 'id'];
		if (!in_array($sortBy, $allowed, true)) {
			$sortBy = 'name';
		}

		$query->orderBy($sortBy, $dir);

		return $query;
	}
}
