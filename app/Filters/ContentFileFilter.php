<?php

namespace App\Filters;

use App\Models\DashboardFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ContentFileFilter
{
	/**
	 * Apply Content Upload UI scopes (tabs, search, sharing, dates).
	 *
	 * @param  Builder<DashboardFile>  $query
	 * @return Builder<DashboardFile>
	 */
	public static function apply(Builder $query, User $user, array $input): Builder
	{
		$tab = $input['tab'] ?? 'all';

		if ($tab === 'archived') {
			$query->whereNotNull('archived_at');
		} else {
			$query->whereNull('archived_at');
		}

		switch ($tab) {
			case 'my_uploads':
				$query->where('uploaded_by', $user->id);
				break;
			case 'team_uploads':
				$query->whereHas(
					'uploader',
					fn (Builder $q) => $q->where('role', 'employee')
				);
				break;
			case 'client_files':
				if ($user->hasRole('admin') || $user->hasRole('employee')) {
					$query->whereHas('shares', function (Builder $q) {
						$q->whereNotNull('shared_with_company_id')
							->orWhere('share_all_employees', true);
					});
				} else {
					$companyIds = $user->companies()->pluck('companies.id');
					$query->whereHas('shares', function (Builder $q) use ($user, $companyIds) {
						$q->where('shared_with_user_id', $user->id)
							->orWhere('share_all_employees', true)
							->when($companyIds->isNotEmpty(), function (Builder $inner) use ($companyIds) {
								$inner->orWhereIn('shared_with_company_id', $companyIds);
							});
					});
				}
				break;
			case 'all':
			case 'archived':
			default:
				if (!$user->hasRole('admin')) {
					$companyIds = $user->companies()->pluck('companies.id');
					$query->where(function (Builder $outer) use ($user, $companyIds) {
						$outer->where('uploaded_by', $user->id)
							->orWhereHas('shares', function (Builder $sh) use ($user, $companyIds) {
								$sh->where('shared_with_user_id', $user->id)
									->orWhere('share_all_employees', true)
									->when($companyIds->isNotEmpty(), function (Builder $inner) use ($companyIds) {
										$inner->orWhereIn('shared_with_company_id', $companyIds);
									});
							});
					});
				}
				break;
		}

		if (!empty($input['search'])) {
			$search = $input['search'];
			$query->where('title', 'like', "%{$search}%");
		}

		if (!empty($input['category'])) {
			$query->where('category', $input['category']);
		}

		if (!empty($input['uploaded_by'])) {
			$query->where('uploaded_by', (int) $input['uploaded_by']);
		}

		if (!empty($input['shared_company_id'])) {
			$query->whereHas(
				'shares',
				fn (Builder $q) => $q->where(
					'shared_with_company_id',
					(int) $input['shared_company_id']
				)
			);
		}

		if (!empty($input['shared_user_id'])) {
			$query->whereHas(
				'shares',
				fn (Builder $q) => $q->where(
					'shared_with_user_id',
					(int) $input['shared_user_id']
				)
			);
		}

		if (array_key_exists('share_all_employees', $input)
			&& filter_var($input['share_all_employees'], FILTER_VALIDATE_BOOLEAN)) {
			$query->whereHas(
				'shares',
				fn (Builder $q) => $q->where('share_all_employees', true)
			);
		}

		if (!empty($input['updated_from'])) {
			$query->whereDate('updated_at', '>=', $input['updated_from']);
		}

		if (!empty($input['updated_to'])) {
			$query->whereDate('updated_at', '<=', $input['updated_to']);
		}

		$sortBy = $input['sort_by'] ?? 'updated_at';
		$sortDir = strtolower((string) ($input['sort_dir'] ?? 'desc')) === 'asc'
			? 'asc'
			: 'desc';

		$allowedSort = ['updated_at', 'created_at', 'title', 'id'];
		if (!in_array($sortBy, $allowedSort, true)) {
			$sortBy = 'updated_at';
		}

		$query->orderBy($sortBy, $sortDir);

		return $query;
	}
}
