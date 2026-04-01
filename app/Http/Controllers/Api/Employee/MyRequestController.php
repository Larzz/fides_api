<?php

namespace App\Http\Controllers\Api\Employee;

use App\Enums\RequestWorkflowStatus;
use App\Filters\RequestFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\PendingRequests\MyRequestsListRequest;
use App\Http\Requests\PendingRequests\StoreRequestRequest;
use App\Http\Resources\PendingRequests\RequestResource;
use App\Http\Responses\ApiResponse;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MyRequestController extends Controller
{
	public function __construct(
		protected ServiceRequestManager $serviceRequestManager
	) {
	}

	/**
	 * List the authenticated user's submissions with optional filters.
	 */
	public function index(MyRequestsListRequest $request): JsonResponse
	{
		$filters = $request->validated();
		unset($filters['user_type']);

		$query = ServiceRequest::query()
			->where('user_id', $request->user()->id)
			->with(['requestType', 'user']);

		RequestFilter::apply($query, $filters);

		$perPage = (int) ($filters['per_page'] ?? 10);
		$paginator = $query->paginate($perPage)->appends($request->query());

		$paginator->through(
			fn (ServiceRequest $row) => (new RequestResource($row))->resolve()
		);

		return ApiResponse::success(
			$paginator->items(),
			'Your requests retrieved',
			200,
			$this->paginationWithLinks($paginator)
		);
	}

	/**
	 * Store a new request with optional file uploads (multipart/form-data).
	 */
	public function store(StoreRequestRequest $request): JsonResponse
	{
		$this->authorize('create', ServiceRequest::class);

		$validated = $request->validated();
		$files = $request->file('attachments', []);
		if (!is_array($files)) {
			$files = $files ? [$files] : [];
		}

		$created = $this->serviceRequestManager->createSubmission(
			$request->user(),
			(int) $validated['request_type_id'],
			$validated['details'],
			$files
		);

		return ApiResponse::created(
			(new RequestResource($created))->resolve(),
			'Request submitted'
		);
	}

	/**
	 * Show a single owned request with attachments.
	 */
	public function show(Request $request, ServiceRequest $serviceRequest): JsonResponse
	{
		abort_unless(
			$serviceRequest->user_id === $request->user()->id,
			403,
			'You may only view your own requests here.'
		);

		$serviceRequest->load([
			'requestType',
			'user',
			'attachments',
			'reviewer',
		]);

		return ApiResponse::success(
			(new RequestResource($serviceRequest))->resolve(),
			'Request retrieved'
		);
	}

	/**
	 * Delete a pending submission owned by the user.
	 */
	public function destroy(Request $request, ServiceRequest $serviceRequest): JsonResponse
	{
		abort_unless(
			$serviceRequest->user_id === $request->user()->id,
			403,
			'You may only delete your own requests here.'
		);

		if ($serviceRequest->status !== RequestWorkflowStatus::Pending) {
			throw ValidationException::withMessages([
				'status' => ['Only pending requests can be deleted.'],
			]);
		}

		$this->serviceRequestManager->deleteRequest($serviceRequest);

		return ApiResponse::success(null, 'Request deleted');
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
