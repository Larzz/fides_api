<?php

namespace App\Http\Controllers\Api;

use App\Filters\WorkToolFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkTool\ListWorkToolsRequest;
use App\Http\Requests\WorkTool\StoreWorkToolRequest;
use App\Http\Requests\WorkTool\UpdateWorkToolRequest;
use App\Http\Resources\WorkToolResource;
use App\Http\Responses\ApiResponse;
use App\Models\WorkTool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkToolController extends Controller
{
	public function stats(Request $request): JsonResponse
	{
		$this->authorize('viewAny', WorkTool::class);

		return ApiResponse::success([
			'active_subscriptions' => WorkTool::query()
				->where('status', 'active')
				->count(),
			'monthly_cost_aed' => (string) WorkTool::query()
				->where('currency', 'AED')
				->where('billing_type', 'monthly')
				->sum('cost'),
			'annual_cost_aed' => (string) WorkTool::query()
				->where('currency', 'AED')
				->where('billing_type', 'annual')
				->sum('cost'),
		], 'Work tool stats retrieved');
	}

	public function index(ListWorkToolsRequest $request): JsonResponse
	{
		$this->authorize('viewAny', WorkTool::class);

		$query = WorkTool::query();
		WorkToolFilter::apply($query, $request->validated());

		$perPage = (int) $request->input('per_page', 10);
		$paginator = $query->paginate($perPage)->appends($request->query());
		$paginator->through(
			fn (WorkTool $row) => (new WorkToolResource($row))->resolve()
		);

		return ApiResponse::success(
			$paginator->items(),
			'Work tools retrieved',
			200,
			$this->paginationWithLinks($paginator)
		);
	}

	public function store(StoreWorkToolRequest $request): JsonResponse
	{
		$this->authorize('create', WorkTool::class);

		$tool = WorkTool::create($request->validated());

		return ApiResponse::created(
			(new WorkToolResource($tool))->resolve(),
			'Work tool created'
		);
	}

	public function show(WorkTool $workTool): JsonResponse
	{
		$this->authorize('view', $workTool);

		return ApiResponse::success(
			(new WorkToolResource($workTool))->resolve(),
			'Work tool retrieved'
		);
	}

	public function update(
		UpdateWorkToolRequest $request,
		WorkTool $workTool
	): JsonResponse {
		$this->authorize('update', $workTool);

		$workTool->update($request->validated());

		return ApiResponse::success(
			(new WorkToolResource($workTool->fresh()))->resolve(),
			'Work tool updated'
		);
	}

	public function destroy(WorkTool $workTool): JsonResponse
	{
		$this->authorize('delete', $workTool);
		$workTool->delete();

		return ApiResponse::success(null, 'Work tool deleted');
	}

	/**
	 * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $paginator
	 */
	protected function paginationWithLinks($paginator): array
	{
		return [
			'pagination' => [
				'current_page' => $paginator->currentPage(),
				'per_page' => $paginator->perPage(),
				'total' => $paginator->total(),
				'last_page' => $paginator->lastPage(),
				'from' => $paginator->firstItem(),
				'to' => $paginator->lastItem(),
			],
			'links' => [
				'first' => $paginator->url(1),
				'last' => $paginator->url($paginator->lastPage()),
				'prev' => $paginator->previousPageUrl(),
				'next' => $paginator->nextPageUrl(),
			],
		];
	}
}
