<?php

namespace App\Http\Controllers\Api;

use App\Filters\ContentFileFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\ListFilesRequest;
use App\Http\Requests\File\ShareFileRequest;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Requests\File\UpdateFileArchiveRequest;
use App\Http\Requests\File\UpdateFileNotifyRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\FileShareResource;
use App\Http\Responses\ApiResponse;
use App\Models\Activity;
use App\Models\DashboardFile;
use App\Models\FileShare;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
	/**
	 * Paginated file list with Content Upload UI filters and tabs.
	 */
	public function index(ListFilesRequest $request): JsonResponse
	{
		$query = DashboardFile::query()
			->with([
				'uploader',
				'shares.sharedWithUser',
				'shares.sharedWithCompany',
			]);

		ContentFileFilter::apply($query, $request->user(), $request->validated());

		$perPage = (int) ($request->input('per_page', 10));
		$paginator = $query->paginate($perPage)->appends($request->query());
		$paginator->through(fn (DashboardFile $row) => (new FileResource($row))->resolve());

		return ApiResponse::success(
			$paginator->items(),
			'Files retrieved successfully',
			200,
			$this->paginationWithLinks($paginator)
		);
	}

	/**
	 * Summary counts for dashboard cards (archived / active).
	 */
	public function stats(ListFilesRequest $request): JsonResponse
	{
		$input = $request->validated();

		$archivedQuery = DashboardFile::query();
		ContentFileFilter::apply(
			$archivedQuery,
			$request->user(),
			array_merge($input, ['tab' => 'archived'])
		);

		$activeQuery = DashboardFile::query();
		ContentFileFilter::apply(
			$activeQuery,
			$request->user(),
			array_merge($input, ['tab' => 'all'])
		);

		return ApiResponse::success([
			'archived_count' => $archivedQuery->count(),
			'active_count' => $activeQuery->count(),
		], 'File stats retrieved');
	}

	/**
	 * Export filtered files as CSV (respects same query params as index).
	 */
	public function export(ListFilesRequest $request): StreamedResponse
	{
		$query = DashboardFile::query()->with(['uploader']);
		ContentFileFilter::apply($query, $request->user(), $request->validated());

		$fileName = 'files-'.now()->format('YmdHis').'.csv';
		$headers = [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => "attachment; filename={$fileName}",
		];

		return response()->stream(function () use ($query) {
			$handle = fopen('php://output', 'w');
			fputcsv($handle, [
				'id',
				'title',
				'category',
				'uploaded_by',
				'updated_at',
				'archived_at',
			]);

			$query->orderBy('id')->chunk(200, function ($rows) use ($handle) {
				foreach ($rows as $row) {
					fputcsv($handle, [
						$row->id,
						$row->title,
						$row->category,
						$row->uploaded_by,
						$row->updated_at,
						$row->archived_at,
					]);
				}
			});

			fclose($handle);
		}, 200, $headers);
	}

	/**
	 * Upload a file (multipart).
	 */
	public function store(StoreFileRequest $request): JsonResponse
	{
		$path = $request->file('file')->store('dashboard-files', 'public');

		$file = DashboardFile::create([
			'title' => $request->string('title')->toString(),
			'category' => $request->input('category'),
			'file_path' => $path,
			'uploaded_by' => $request->user()->id,
			'notify_stakeholders' => $request->boolean('notify_stakeholders', true),
		]);

		Activity::create([
			'user_id' => $request->user()->id,
			'action' => 'file_uploaded',
			'description' => 'File uploaded: '.$file->title,
			'ip_address' => $request->ip(),
			'created_at' => now(),
		]);

		return ApiResponse::created(
			new FileResource($file->load(['uploader', 'shares'])),
			'File uploaded successfully'
		);
	}

	/**
	 * Toggle file-level notification flag (UI notify switch).
	 */
	public function updateNotify(
		UpdateFileNotifyRequest $request,
		DashboardFile $file
	): JsonResponse {
		$this->authorizeFileManage($request, $file);

		$file->update([
			'notify_stakeholders' => $request->boolean('notify_stakeholders'),
		]);

		return ApiResponse::success(
			new FileResource($file->load(['uploader', 'shares'])),
			'Notification preference updated'
		);
	}

	/**
	 * Archive or restore a content file.
	 */
	public function updateArchive(
		UpdateFileArchiveRequest $request,
		DashboardFile $file
	): JsonResponse {
		$this->authorizeFileManage($request, $file);

		$file->update([
			'archived_at' => $request->boolean('archived') ? now() : null,
		]);

		return ApiResponse::success(
			new FileResource($file->load(['uploader', 'shares'])),
			'Archive state updated'
		);
	}

	/**
	 * File detail including shares.
	 */
	public function show(DashboardFile $file): JsonResponse
	{
		return ApiResponse::success(
			new FileResource($file->load([
				'uploader',
				'shares.sharedWithUser',
				'shares.sharedWithCompany',
			])),
			'File retrieved successfully'
		);
	}

	/**
	 * Soft-delete file and remove storage blob.
	 */
	public function destroy(Request $request, DashboardFile $file): JsonResponse
	{
		$isOwner = $file->uploaded_by === $request->user()->id;
		$isAdmin = $request->user()->hasRole('admin');
		abort_unless($isOwner || $isAdmin, 403, 'Insufficient permissions');

		Storage::disk('public')->delete($file->file_path);
		$file->delete();

		Activity::create([
			'user_id' => $request->user()->id,
			'action' => 'file_deleted',
			'description' => 'File deleted: '.$file->title,
			'ip_address' => $request->ip(),
			'created_at' => now(),
		]);

		return ApiResponse::success(null, 'File deleted successfully');
	}

	/**
	 * Add or adjust sharing targets for a file.
	 */
	public function share(
		ShareFileRequest $request,
		DashboardFile $file
	): JsonResponse {
		$all = $request->boolean('share_all_employees');
		$share = FileShare::create([
			'file_id' => $file->id,
			'share_all_employees' => $all,
			'shared_with_user_id' => $all ? null : $request->input('shared_with_user_id'),
			'shared_with_company_id' => $all ? null : $request->input('shared_with_company_id'),
			'notifications_enabled' => (bool) $request->input(
				'notifications_enabled',
				true
			),
			'created_at' => now(),
		]);

		return ApiResponse::created(
			new FileShareResource($share),
			'File shared successfully'
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

	protected function authorizeFileManage(Request $request, DashboardFile $file): void
	{
		$isOwner = $file->uploaded_by === $request->user()->id;
		$isAdmin = $request->user()->hasRole('admin');
		abort_unless($isOwner || $isAdmin, 403, 'Insufficient permissions');
	}
}
