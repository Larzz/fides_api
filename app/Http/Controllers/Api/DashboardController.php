<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Activity;
use App\Models\Approval;
use App\Models\DashboardFile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
	public function index(): JsonResponse
	{
		$uploadsLast7Days = DashboardFile::query()
			->where('created_at', '>=', now()->subDays(7))
			->count();

		$data = [
			'pending_approvals_count' => Approval::where('status', 'pending')->count(),
			'active_users_count' => User::count(),
			'uploads_last_7_days' => $uploadsLast7Days,
			'logins_today' => Activity::query()
				->where('action', 'logged_in')
				->whereDate('created_at', today())
				->count(),
		];

		return ApiResponse::success($data, 'Dashboard metrics retrieved successfully');
	}
}