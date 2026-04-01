<?php

namespace App\Http\Controllers\Api;

use App\Filters\AccessFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Access\AssignAccessUsersRequest;
use App\Http\Requests\Access\ListAccessesRequest;
use App\Http\Requests\Access\StoreAccessRequest;
use App\Http\Requests\Access\UpdateAccessRequest;
use App\Http\Resources\AccessResource;
use App\Http\Responses\ApiResponse;
use App\Models\Access;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessController extends Controller
{
	public function stats(Request $request): JsonResponse
	{
		$this->authorize('viewAny', Access::class);

		return ApiResponse::success([
			'active_access_count' => Access::query()
				->where('status', 'active')
				->count(),
		], 'Access stats retrieved');
	}

	public function index(ListAccessesRequest $request): JsonResponse
	{
		$this->authorize('viewAny', Access::class);

		$query = Access::query()->with(['company', 'users']);
		AccessFilter::apply($query, $request->validated());

		$perPage = (int) $request->input('per_page', 10);
		$paginator = $query->paginate($perPage)->appends($request->query());
		$paginator->through(
			fn (Access $row) => (new AccessResource($row))->resolve()
		);

		return ApiResponse::success(
			$paginator->items(),
			'Access records retrieved',
			200,
			$this->paginationWithLinks($paginator)
		);
	}

	public function store(StoreAccessRequest $request): JsonResponse
	{
		$this->authorize('create', Access::class);

		$data = $request->validated();
		$userIds = $data['user_ids'] ?? [];
		unset($data['user_ids']);

		$access = Access::create($data);
		if ($userIds !== []) {
			$access->users()->sync($userIds);
		}

		$access->load(['company', 'users']);

		return ApiResponse::created(
			(new AccessResource($access))->resolve(),
			'Access created'
		);
	}

	public function show(Access $access): JsonResponse
	{
		$this->authorize('view', $access);

		return ApiResponse::success(
			(new AccessResource($access->load(['company', 'users'])))->resolve(),
			'Access retrieved'
		);
	}

	public function update(UpdateAccessRequest $request, Access $access): JsonResponse
	{
		$this->authorize('update', $access);

		$access->update($request->validated());
		$access->load(['company', 'users']);

		return ApiResponse::success(
			(new AccessResource($access))->resolve(),
			'Access updated'
		);
	}

	public function destroy(Access $access): JsonResponse
	{
		$this->authorize('delete', $access);
		$access->delete();

		return ApiResponse::success(null, 'Access deleted');
	}

	public function assignUsers(
		AssignAccessUsersRequest $request,
		Access $access
	): JsonResponse {
		$this->authorize('assignUsers', $access);

		$payload = $request->validated();
		$mode = $payload['mode'] ?? 'attach';
		$ids = $payload['user_ids'];

		if ($mode === 'sync') {
			$access->users()->sync($ids);
		} else {
			$access->users()->syncWithoutDetaching($ids);
		}

		$access->load(['company', 'users']);

		return ApiResponse::success(
			(new AccessResource($access))->resolve(),
			'Users assigned to access'
		);
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
