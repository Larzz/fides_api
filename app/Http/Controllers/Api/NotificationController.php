<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Resources\DashboardNotificationResource;
use App\Http\Responses\ApiResponse;
use App\Models\DashboardNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
	public function index(Request $request): JsonResponse
	{
		$notifications = DashboardNotification::query()
			->where('user_id', $request->user()->id)
			->latest('created_at')
			->paginate((int) $request->input('per_page', 15));

		$notifications->through(
			fn ($notification) => new DashboardNotificationResource($notification)
		);

		return ApiResponse::paginated(
			$notifications,
			'Notifications retrieved successfully'
		);
	}

	public function store(StoreNotificationRequest $request): JsonResponse
	{
		$notification = DashboardNotification::create([
			...$request->validated(),
			'created_at' => now(),
		]);

		return ApiResponse::created(
			new DashboardNotificationResource($notification),
			'Notification created successfully'
		);
	}

	public function read(Request $request, DashboardNotification $notification): JsonResponse
	{
		abort_unless(
			$notification->user_id === $request->user()->id,
			403,
			'Insufficient permissions'
		);

		$notification->update([
			'read_at' => now(),
		]);

		return ApiResponse::success(
			new DashboardNotificationResource($notification),
			'Notification marked as read'
		);
	}
}
