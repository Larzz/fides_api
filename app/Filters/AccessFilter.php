<?php

namespace App\Filters;

use App\Models\Access;
use Illuminate\Database\Eloquent\Builder;

class AccessFilter
{
	/**
	 * @param  Builder<Access>  $query
	 * @return Builder<Access>
	 */
	public static function apply(Builder $query, array $input): Builder
	{
		if (!empty($input['search'])) {
			$s = $input['search'];
			$query->where('name', 'like', "%{$s}%");
		}

		if (!empty($input['company_id'])) {
			$query->where('company_id', (int) $input['company_id']);
		}

		if (!empty($input['platform'])) {
			$query->where('platform', $input['platform']);
		}

		if (!empty($input['status'])) {
			$query->where('status', $input['status']);
		}

		$query->orderBy('name');

		return $query;
	}
}
