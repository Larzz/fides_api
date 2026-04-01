<?php

namespace App\Filters;

use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Builder;

class SystemLogFilter
{
	/**
	 * @param  Builder<SystemLog>  $query
	 * @return Builder<SystemLog>
	 */
	public static function apply(Builder $query, array $input): Builder
	{
		if (!empty($input['search'])) {
			$s = $input['search'];
			$query->where(function (Builder $outer) use ($s) {
				$outer->where('details', 'like', "%{$s}%")
					->orWhereHas('user', function (Builder $userQuery) use ($s) {
						$userQuery->where('name', 'like', "%{$s}%");
					});
			});
		}

		if (!empty($input['action'])) {
			$query->where('action', $input['action']);
		}

		if (!empty($input['user_type'])) {
			$query->where('user_type', $input['user_type']);
		}

		if (!empty($input['date_from'])) {
			$query->whereDate('created_at', '>=', $input['date_from']);
		}

		if (!empty($input['date_to'])) {
			$query->whereDate('created_at', '<=', $input['date_to']);
		}

		$query->orderByDesc('created_at');

		return $query;
	}
}
