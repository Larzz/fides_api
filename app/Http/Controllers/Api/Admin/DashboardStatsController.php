<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\RequestWorkflowStatus;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardStatsController extends Controller
{
	/**
	 * Aggregate counters for the admin Pending Requests dashboard.
	 */
	public function stats(Request $request): JsonResponse
	{
		$totalPending = ServiceRequest::query()
			->byStatus(RequestWorkflowStatus::Pending)
			->count();

		$breakdownByType = ServiceRequest::query()
			->select([
				'request_types.id as request_type_id',
				'request_types.name as request_type_name',
				DB::raw('COUNT(*) as count'),
			])
			->join('request_types', 'request_types.id', '=', 'requests.request_type_id')
			->where('requests.status', RequestWorkflowStatus::Pending->value)
			->groupBy('request_types.id', 'request_types.name')
			->orderBy('request_types.name')
			->get();

		$breakdownByStatus = ServiceRequest::query()
			->select(['status', DB::raw('COUNT(*) as count')])
			->groupBy('status')
			->orderBy('status')
			->get();

		return ApiResponse::success([
			'total_pending' => $totalPending,
			'breakdown_by_request_type' => $breakdownByType,
			'breakdown_by_status' => $breakdownByStatus,
		], 'Dashboard statistics retrieved');
	}
}
