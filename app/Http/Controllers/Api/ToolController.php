<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tool\StoreToolRequest;
use App\Http\Requests\Tool\UpdateToolRequest;
use App\Http\Requests\Tool\AssignUsersRequest;
use App\Http\Requests\Tool\AddCostRequest;
use App\Http\Resources\ToolResource;
use App\Http\Responses\ApiResponse;
use App\Services\ToolService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToolController extends Controller
{
	public function __construct(
		protected ToolService $toolService
	) {
	}

	/**
	 * Display a listing of tools
	 */
	public function index(Request $request): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$tools = $this->toolService->getAllTools($perPage);

		return ApiResponse::paginated(
			ToolResource::collection($tools),
			'Tools retrieved successfully'
		);
	}

	/**
	 * Store a newly created tool
	 */
	public function store(StoreToolRequest $request): JsonResponse
	{
		$data = $request->validated();
		$data['user_id'] = $request->user()->id;

		if ($request->hasFile('image')) {
			$data['image'] = $request->file('image')->store('tools/images', 'public');
		}

		$tool = $this->toolService->createTool($data);

		return ApiResponse::created(
			new ToolResource($tool->load(['categoryRelation', 'statusRelation', 'users', 'toolTags'])),
			'Tool created successfully'
		);
	}

	/**
	 * Display the specified tool
	 */
	public function show(int $id): JsonResponse
	{
		$tool = $this->toolService->getToolById($id);

		return ApiResponse::success(
			new ToolResource($tool->load([
				'categoryRelation',
				'statusRelation',
				'users',
				'toolTags',
				'toolNotes',
				'toolCosts',
				'toolBillings',
			])),
			'Tool retrieved successfully'
		);
	}

	/**
	 * Update the specified tool
	 */
	public function update(UpdateToolRequest $request, int $id): JsonResponse
	{
		$data = $request->validated();

		if ($request->hasFile('image')) {
			$data['image'] = $request->file('image')->store('tools/images', 'public');
		}

		$tool = $this->toolService->updateTool($id, $data);

		return ApiResponse::success(
			new ToolResource($tool->load(['categoryRelation', 'statusRelation', 'users', 'toolTags'])),
			'Tool updated successfully'
		);
	}

	/**
	 * Remove the specified tool
	 */
	public function destroy(int $id): JsonResponse
	{
		$this->toolService->deleteTool($id);

		return ApiResponse::success(
			null,
			'Tool deleted successfully'
		);
	}

	/**
	 * Assign users to tool
	 */
	public function assignUsers(AssignUsersRequest $request, int $id): JsonResponse
	{
		$tool = $this->toolService->assignUsers($id, $request->validated()['user_ids']);

		return ApiResponse::success(
			new ToolResource($tool->load('users')),
			'Users assigned successfully'
		);
	}

	/**
	 * Add note to tool
	 */
	public function addNote(Request $request, int $id): JsonResponse
	{
		$request->validate([
			'note' => ['required', 'string', 'max:1000'],
		]);

		$tool = $this->toolService->addNote($id, $request->input('note'));

		return ApiResponse::success(
			new ToolResource($tool->load('toolNotes')),
			'Note added successfully'
		);
	}

	/**
	 * Add cost to tool
	 */
	public function addCost(AddCostRequest $request, int $id): JsonResponse
	{
		$tool = $this->toolService->addCost($id, $request->validated());

		return ApiResponse::success(
			new ToolResource($tool->load('toolCosts')),
			'Cost added successfully'
		);
	}

	/**
	 * Add billing to tool
	 */
	public function addBilling(Request $request, int $id): JsonResponse
	{
		$request->validate([
			'name' => ['required', 'string', 'max:255'],
			'description' => ['nullable', 'string', 'max:500'],
			'amount' => ['required', 'numeric', 'min:0'],
			'currency' => ['required', 'string', 'max:3'],
			'billing_date' => ['required', 'date'],
			'billing_period' => ['nullable', 'string', 'max:50'],
		]);

		$tool = $this->toolService->addBilling($id, $request->only([
			'name',
			'description',
			'amount',
			'currency',
			'billing_date',
			'billing_period',
		]));

		return ApiResponse::success(
			new ToolResource($tool->load('toolBillings')),
			'Billing added successfully'
		);
	}

	/**
	 * Get tools by category
	 */
	public function getByCategory(Request $request, string $category): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$tools = $this->toolService->getToolsByCategory($category, $perPage);

		return ApiResponse::paginated(
			ToolResource::collection($tools),
			'Tools retrieved successfully'
		);
	}

	/**
	 * Get tools by status
	 */
	public function getByStatus(Request $request, string $status): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$tools = $this->toolService->getToolsByStatus($status, $perPage);

		return ApiResponse::paginated(
			ToolResource::collection($tools),
			'Tools retrieved successfully'
		);
	}

	/**
	 * Get tools by user
	 */
	public function getByUser(Request $request, int $userId): JsonResponse
	{
		$perPage = $request->get('per_page', 15);
		$tools = $this->toolService->getToolsByUser($userId, $perPage);

		return ApiResponse::paginated(
			ToolResource::collection($tools),
			'Tools retrieved successfully'
		);
	}

	/**
	 * Search tools
	 */
	public function search(Request $request): JsonResponse
	{
		$request->validate([
			'query' => ['required', 'string', 'min:2'],
		]);

		$perPage = $request->get('per_page', 15);
		$tools = $this->toolService->searchTools($request->input('query'), $perPage);

		return ApiResponse::paginated(
			ToolResource::collection($tools),
			'Tools found successfully'
		);
	}
}

