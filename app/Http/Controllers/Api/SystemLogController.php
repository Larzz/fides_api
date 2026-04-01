<?php

namespace App\Http\Controllers\Api;

use App\Filters\SystemLogFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemLog\ListSystemLogsRequest;
use App\Http\Resources\SystemLogResource;
use App\Http\Responses\ApiResponse;
use App\Models\SystemLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SystemLogController extends Controller
{
	public function stats(Request $request): JsonResponse
	{
		$this->authorize('viewAny', SystemLog::class);

		$today = SystemLog::query()->whereDate('created_at', today())->count();

		return ApiResponse::success([
			'activities_logged_today' => $today,
		], 'System log stats retrieved');
	}

	public function index(ListSystemLogsRequest $request): JsonResponse
	{
		$this->authorize('viewAny', SystemLog::class);

		$query = SystemLog::query()->with('user');
		SystemLogFilter::apply($query, $request->validated());

		$perPage = (int) $request->input('per_page', 10);
		$paginator = $query->paginate($perPage)->appends($request->query());
		$paginator->through(
			fn (SystemLog $row) => (new SystemLogResource($row))->resolve()
		);

		return ApiResponse::success(
			$paginator->items(),
			'System logs retrieved',
			200,
			$this->paginationWithLinks($paginator)
		);
	}

	public function export(ListSystemLogsRequest $request): StreamedResponse
	{
		$this->authorize('export', SystemLog::class);

		$query = SystemLog::query()->with('user');
		SystemLogFilter::apply($query, $request->validated());

		$fileName = 'system-logs-'.now()->format('YmdHis').'.csv';
		$headers = [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => "attachment; filename={$fileName}",
		];

		return response()->stream(function () use ($query) {
			$handle = fopen('php://output', 'w');
			fputcsv($handle, [
				'id',
				'created_at',
				'action',
				'user_type',
				'user_name',
				'details',
			]);

			$query->orderByDesc('created_at')->chunk(200, function ($rows) use ($handle) {
				foreach ($rows as $row) {
					fputcsv($handle, [
						$row->id,
						$row->created_at,
						$row->action,
						$row->user_type,
						$row->user?->name,
						$row->details,
					]);
				}
			});

			fclose($handle);
		}, 200, $headers);
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
