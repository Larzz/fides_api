<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Content\UploadContentRequest;
use App\Http\Requests\Content\ShareContentRequest;
use App\Http\Resources\ContentResource;
use App\Http\Responses\ApiResponse;
use App\Services\ContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
	public function __construct(
		protected ContentService $contentService
	) {
	}

	/**
	 * Display a listing of content
	 */
	public function index(Request $request): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$content = $this->contentService->getAllContent($perPage);

		return ApiResponse::paginated(
			ContentResource::collection($content),
			'Content retrieved successfully'
		);
	}

	/**
	 * Store a newly uploaded content
	 */
	public function store(UploadContentRequest $request): JsonResponse
	{
		$upload = $this->contentService->uploadContent(
			$request->user()->id,
			$request->file('file'),
			$request->input('category_id')
		);

		return ApiResponse::created(
			new ContentResource($upload->load(['user', 'category'])),
			'Content uploaded successfully'
		);
	}

	/**
	 * Display the specified content
	 */
	public function show(int $id): JsonResponse
	{
		$content = $this->contentService->getContentById($id);

		return ApiResponse::success(
			new ContentResource($content->load(['user', 'category', 'shares', 'downloads'])),
			'Content retrieved successfully'
		);
	}

	/**
	 * Remove the specified content
	 */
	public function destroy(int $id): JsonResponse
	{
		$this->contentService->deleteContent($id);

		return ApiResponse::success(
			null,
			'Content deleted successfully'
		);
	}

	/**
	 * Download content
	 */
	public function download(int $id)
	{
		$download = $this->contentService->downloadContent($id, request()->user()->id);

		$content = $this->contentService->getContentById($id);

		if (!Storage::disk('public')->exists($content->file_path)) {
			return ApiResponse::error('File not found', 404);
		}

		return response()->download(
			Storage::disk('public')->path($content->file_path),
			$content->file_name
		);
	}

	/**
	 * Share content
	 */
	public function share(ShareContentRequest $request, int $id): JsonResponse
	{
		$share = $this->contentService->shareContent(
			$id,
			$request->user()->id,
			$request->validated()['shared_with_user_id']
		);

		return ApiResponse::success(
			$share,
			'Content shared successfully'
		);
	}

	/**
	 * Get content by user
	 */
	public function getByUser(Request $request, int $userId): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$content = $this->contentService->getContentByUser($userId, $perPage);

		return ApiResponse::paginated(
			ContentResource::collection($content),
			'Content retrieved successfully'
		);
	}

	/**
	 * Get content by category
	 */
	public function getByCategory(Request $request, int $categoryId): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$content = $this->contentService->getContentByCategory($categoryId, $perPage);

		return ApiResponse::paginated(
			ContentResource::collection($content),
			'Content retrieved successfully'
		);
	}

	/**
	 * Get content by file type
	 */
	public function getByFileType(Request $request, string $fileType): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$content = $this->contentService->getContentByFileType($fileType, $perPage);

		return ApiResponse::paginated(
			ContentResource::collection($content),
			'Content retrieved successfully'
		);
	}

	/**
	 * Search content
	 */
	public function search(Request $request): JsonResponse
	{
		$request->validate([
			'query' => ['required', 'string', 'min:2'],
		]);

		$perPage = $request->get('per_page', 15);
		$content = $this->contentService->searchContent($request->input('query'), $perPage);

		return ApiResponse::paginated(
			ContentResource::collection($content),
			'Content found successfully'
		);
	}
}

