<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AssignEmployeesRequest;
use App\Http\Requests\Client\IndexClientRequest;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ContractResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Responses\ApiResponse;
use App\Models\Company;
use App\Models\Contract;
use App\Services\ContractNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientController extends Controller
{
	public function index(IndexClientRequest $request): JsonResponse
	{
		$this->authorize('viewAny', Company::class);

		$query = Company::query()->withCount('contracts');
		$user = $request->user();

		if (!$user->hasRole('admin')) {
			if ($user->hasAnyRole(['employee', 'client'])) {
				$query->whereHas('users', function ($q) use ($user) {
					$q->where('users.id', $user->id);
				});
			}
		}

		$filters = $request->validated();

		if (!empty($filters['search'])) {
			$search = $filters['search'];
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('primary_contact_email', 'like', "%{$search}%");
			});
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['assigned_employees'])) {
			$uid = (int) $filters['assigned_employees'];
			$query->whereHas('users', function ($q) use ($uid) {
				$q->where('users.id', $uid);
			});
		}

		if (!empty($filters['contract_status'])) {
			$contractStatus = $filters['contract_status'];
			$query->whereHas('contracts', function ($q) use ($contractStatus) {
				$q->where('status', $contractStatus);
			});
		}

		$clients = $query->latest()->paginate((int) ($filters['per_page'] ?? 10));
		$clients->through(fn ($row) => new ClientResource($row));

		return ApiResponse::paginated($clients, 'Clients retrieved successfully');
	}

	public function store(StoreClientRequest $request): JsonResponse
	{
		$this->authorize('create', Company::class);

		$company = Company::create($request->validated());

		return ApiResponse::created(
			new ClientResource($company),
			'Client created successfully'
		);
	}

	public function show(Company $client): JsonResponse
	{
		$this->authorize('view', $client);
		$client->load(['contracts', 'users']);

		return ApiResponse::success(
			new ClientResource($client),
			'Client retrieved successfully'
		);
	}

	public function update(UpdateClientRequest $request, Company $client): JsonResponse
	{
		$this->authorize('update', $client);
		$client->update($request->validated());
		$client->load(['contracts', 'users']);

		return ApiResponse::success(
			new ClientResource($client),
			'Client updated successfully'
		);
	}

	public function destroy(Company $client): JsonResponse
	{
		$this->authorize('delete', $client);
		$client->delete();

		return ApiResponse::success(null, 'Client deleted successfully');
	}

	public function assignEmployees(
		AssignEmployeesRequest $request,
		Company $client
	): JsonResponse {
		$this->authorize('assignEmployees', $client);

		$userIds = $request->validated()['user_ids'];
		$syncData = [];
		foreach ($userIds as $userId) {
			$syncData[$userId] = ['assigned_at' => now()];
		}

		$client->users()->sync($syncData);
		$client->load(['users', 'contracts']);

		return ApiResponse::success(
			new ClientResource($client),
			'Employees assigned successfully'
		);
	}

	public function employees(Company $client): JsonResponse
	{
		$this->authorize('view', $client);

		$employees = $client->users()
			->orderBy('name')
			->paginate(10);
		$employees->through(fn ($row) => new EmployeeResource($row));

		return ApiResponse::paginated(
			$employees,
			'Client employees retrieved successfully'
		);
	}

	public function contracts(Company $client): JsonResponse
	{
		$this->authorize('view', $client);

		$contracts = $client->contracts()
			->orderByDesc('end_date')
			->paginate(10);
		$contracts->through(fn ($row) => new ContractResource($row));

		return ApiResponse::paginated(
			$contracts,
			'Contracts retrieved successfully'
		);
	}

	public function storeContract(
		StoreContractRequest $request,
		Company $client
	): JsonResponse {
		$this->authorize('manageContracts', $client);

		$data = $request->validated();
		$status = Contract::computeStatusForDates(Carbon::parse($data['end_date']));
		$contract = $client->contracts()->create([
			'title' => $data['title'],
			'start_date' => $data['start_date'],
			'end_date' => $data['end_date'],
			'status' => $status,
		]);

		ContractNotificationService::notifyIfNeeded($contract, null, $contract->status);

		return ApiResponse::created(
			new ContractResource($contract),
			'Contract created successfully'
		);
	}

	public function metrics(Request $request): JsonResponse
	{
		$this->authorize('viewMetrics', Company::class);

		$data = [
			'active_clients_count' => Company::where('status', 'active')->count(),
			'contracts_expiring_count' => Contract::where('status', 'expiring')->count(),
		];

		return ApiResponse::success($data, 'Client metrics retrieved successfully');
	}

	public function export(Request $request): StreamedResponse
	{
		$this->authorize('export', Company::class);

		$fileName = 'clients-'.now()->format('YmdHis').'.csv';
		$headers = [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => "attachment; filename={$fileName}",
		];

		return response()->stream(function () {
			$handle = fopen('php://output', 'w');
			fputcsv($handle, [
				'id',
				'name',
				'primary_contact_email',
				'status',
			]);

			Company::query()->orderBy('id')->chunk(200, function ($rows) use ($handle) {
				foreach ($rows as $row) {
					fputcsv($handle, [
						$row->id,
						$row->name,
						$row->primary_contact_email,
						$row->status,
					]);
				}
			});

			fclose($handle);
		}, 200, $headers);
	}
}
