<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\RequestType;
use Illuminate\Http\JsonResponse;

class RequestTypeController extends Controller
{
	/**
	 * Active request types for picklists and forms.
	 */
	public function index(): JsonResponse
	{
		$types = RequestType::query()
			->where('is_active', true)
			->orderBy('name')
			->get(['id', 'name', 'is_active']);

		return ApiResponse::success($types, 'Request types retrieved');
	}
}
