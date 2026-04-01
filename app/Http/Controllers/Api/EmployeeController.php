<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\AssignClientsRequest;
use App\Http\Requests\Employee\IndexEmployeeRequest;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeStatusRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Responses\ApiResponse;
use App\Models\Activity;
use App\Models\DashboardNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
	public function index(IndexEmployeeRequest $request): JsonResponse
	{
		$this->authorize('viewAny', User::class);

		$query = User::query()->with('companies');
		$filters = $request->validated();

		if (!empty($filters['search'])) {
			$search = $filters['search'];
			$query->where(function ($innerQuery) use ($search) {
				$innerQuery
					->where('name', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%");
			});
		}

		if (!empty($filters['role'])) {
			$query->where('role', $filters['role']);
		}

		if (!empty($filters['user_type'])) {
			$query->where('role', $filters['user_type']);
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['assigned_client'])) {
			$companyId = (int) $filters['assigned_client'];
			$query->whereHas('companies', function ($companyQuery) use ($companyId) {
				$companyQuery->where('companies.id', $companyId);
			});
		}

		$employees = $query->latest()->paginate((int) ($filters['per_page'] ?? 10));
		$employees->through(fn ($employee) => new EmployeeResource($employee));

		return ApiResponse::paginated($employees, 'Employees retrieved successfully');
	}

	public function store(StoreEmployeeRequest $request): JsonResponse
	{
		$this->authorize('create', User::class);

		$payload = $request->validated();
		$payload['password'] = Hash::make($payload['password']);
		$payload['notes'] = '';
		$payload['image'] = '';
		$payload['resume'] = '';
		$payload['cover_letter'] = '';

		$employee = User::create($payload);
		$this->logActivity($request, 'create_employee', 'Created employee '.$employee->email);

		return ApiResponse::created(
			new EmployeeResource($employee->load('companies')),
			'Employee created successfully'
		);
	}

	public function show(User $employee): JsonResponse
	{
		$this->authorize('view', $employee);

		return ApiResponse::success(
			new EmployeeResource($employee->load('companies')),
			'Employee retrieved successfully'
		);
	}

	public function update(
		UpdateEmployeeRequest $request,
		User $employee
	): JsonResponse {
		$this->authorize('update', $employee);

		$payload = $request->validated();
		if (array_key_exists('password', $payload)) {
			$payload['password'] = Hash::make($payload['password']);
		}

		$employee->update($payload);
		$this->logActivity($request, 'update_profile', 'Updated employee '.$employee->email);

		return ApiResponse::success(
			new EmployeeResource($employee->load('companies')),
			'Employee updated successfully'
		);
	}

	public function destroy(Request $request, User $employee): JsonResponse
	{
		$this->authorize('delete', $employee);

		$employee->delete();
		$this->logActivity($request, 'delete_employee', 'Deleted employee '.$employee->email);

		return ApiResponse::success(null, 'Employee deleted successfully');
	}

	public function assignClients(
		AssignClientsRequest $request,
		User $employee
	): JsonResponse {
		$this->authorize('assignClients', $employee);

		$companyIds = $request->validated()['company_ids'];
		$syncData = [];
		foreach ($companyIds as $companyId) {
			$syncData[$companyId] = ['assigned_at' => now()];
		}

		$employee->companies()->sync($syncData);
		$employee->load('companies');

		$this->logActivity(
			$request,
			'assign_client',
			'Assigned clients to '.$employee->email
		);

		DashboardNotification::create([
			'user_id' => $employee->id,
			'title' => 'Client Assignment Updated',
			'message' => 'You have been assigned to one or more clients.',
			'type' => 'employee_assigned_client',
			'created_at' => now(),
		]);

		return ApiResponse::success(
			EmployeeResource::make($employee),
			'Clients assigned successfully'
		);
	}

	public function clients(User $employee): JsonResponse
	{
		$this->authorize('view', $employee);

		return ApiResponse::success(
			CompanyResource::collection($employee->companies()->get()),
			'Employee clients retrieved successfully'
		);
	}

	public function updateStatus(
		UpdateEmployeeStatusRequest $request,
		User $employee
	): JsonResponse {
		$this->authorize('updateStatus', $employee);

		$employee->update([
			'status' => $request->validated()['status'],
		]);

		$this->logActivity(
			$request,
			'update_status',
			'Updated status for '.$employee->email.' to '.$employee->status
		);

		DashboardNotification::create([
			'user_id' => $employee->id,
			'title' => 'Status Updated',
			'message' => 'Your status is now '.$employee->status.'.',
			'type' => 'employee_status_changed',
			'created_at' => now(),
		]);

		return ApiResponse::success(
			new EmployeeResource($employee->load('companies')),
			'Employee status updated successfully'
		);
	}

	public function metrics(Request $request): JsonResponse
	{
		$this->authorize('viewMetrics', User::class);

		$base = User::query()->where('role', 'employee');
		$data = [
			'total_employees' => (clone $base)->count(),
			'active_employees' => (clone $base)->where('status', 'active')->count(),
			'on_leave_count' => (clone $base)->where('status', 'on_leave')->count(),
		];

		$this->logActivity($request, 'view_metrics', 'Viewed employee metrics');

		return ApiResponse::success($data, 'Employee metrics retrieved successfully');
	}

	public function export(Request $request): StreamedResponse
	{
		$this->authorize('export', User::class);

		$fileName = 'employees-'.now()->format('YmdHis').'.csv';
		$headers = [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => "attachment; filename={$fileName}",
		];

		$this->logActivity($request, 'export_employee_csv', 'Exported employee CSV');

		return response()->stream(function () {
			$handle = fopen('php://output', 'w');
			fputcsv($handle, ['id', 'name', 'email', 'role', 'job_title', 'status']);

			User::query()
				->where('role', 'employee')
				->orderBy('id')
				->chunk(200, function ($employees) use ($handle) {
					foreach ($employees as $employee) {
						fputcsv($handle, [
							$employee->id,
							$employee->name,
							$employee->email,
							$employee->role,
							$employee->job_title,
							$employee->status,
						]);
					}
				});

			fclose($handle);
		}, 200, $headers);
	}

	private function logActivity(Request $request, string $action, string $description): void
	{
		Activity::create([
			'user_id' => $request->user()?->id,
			'action' => $action,
			'description' => $description,
			'ip_address' => $request->ip(),
			'created_at' => now(),
		]);
	}
}
