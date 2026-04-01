<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Http\Responses\ApiResponse;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
	public function index(Request $request): JsonResponse
	{
		$activities = Activity::query()
			->with('user')
			->latest('created_at')
			->paginate((int) $request->input('per_page', 15));

		$activities->through(fn ($activity) => new ActivityResource($activity));

		return ApiResponse::paginated(
			$activities,
			'Activities retrieved successfully'
		);
	}
}
