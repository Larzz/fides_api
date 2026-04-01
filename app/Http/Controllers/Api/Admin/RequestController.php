<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\RequestWorkflowStatus;
use App\Filters\RequestFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\PendingRequests\AdminListRequestsRequest;
use App\Http\Requests\PendingRequests\UpdateRequestStatusRequest;
use App\Http\Resources\PendingRequests\RequestResource;
use App\Http\Responses\ApiResponse;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestManager;
use Illuminate\Http\JsonResponse;

class RequestController extends Controller
{
	public function __construct(
		protected ServiceRequestManager $serviceRequestManager
	) {
	}

	/**
	 * Paginated admin inbox with filters, sorting, and link metadata.
	 */
	public function index(AdminListRequestsRequest $request): JsonResponse
	{
		$this->authorize('viewAny', ServiceRequest::class);

		$query = ServiceRequest::query()
			->with(['requestType', 'user']);

		RequestFilter::apply($query, $request->validated());

		$perPage = (int) ($request->validated()['per_page'] ?? 10);
		$paginator = $query->paginate($perPage)->appends($request->query());

		$paginator->through(
			fn (ServiceRequest $row) => (new RequestResource($row))->resolve()
		);

		return ApiResponse::success(
			$paginator->items(),
			'Requests retrieved',
			200,
			$this->paginationWithLinks($paginator)
		);
	}

	/**
	 * Full request detail including reviewer and attachment metadata.
	 */
	public function show(ServiceRequest $serviceRequest): JsonResponse
	{
		$this->authorize('view', $serviceRequest);

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
	 * Apply a workflow status transition and optional admin notes.
	 */
	public function updateStatus(
		UpdateRequestStatusRequest $request,
		ServiceRequest $serviceRequest
	): JsonResponse {
		$this->authorize('updateStatus', $serviceRequest);

		$payload = $request->validated();
		$status = RequestWorkflowStatus::from($payload['status']);

		$updated = $this->serviceRequestManager->updateStatus(
			$serviceRequest,
			$request->user(),
			$status,
			$payload['notes'] ?? null
		);

		return ApiResponse::success(
			(new RequestResource($updated))->resolve(),
			'Request status updated'
		);
	}

	/**
	 * Permanently delete a request and its stored attachments (admin).
	 */
	public function destroy(ServiceRequest $serviceRequest): JsonResponse
	{
		$this->authorize('delete', $serviceRequest);

		$this->serviceRequestManager->deleteRequest($serviceRequest);

		return ApiResponse::success(null, 'Request deleted');
	}

	/**
	 * Build standard pagination + URL link meta for JSON envelopes.
	 *
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
