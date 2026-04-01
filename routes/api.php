<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\DashboardStatsController;
use App\Http\Controllers\Api\Admin\RequestController as AdminPendingRequestController;
use App\Http\Controllers\Api\Auth\PendingAuthController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Employee\MyRequestController;
use App\Http\Controllers\Api\RequestTypeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\ToolController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\AccessController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SystemLogController;
use App\Http\Controllers\Api\WorkToolController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\EmployeeController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/login', [PendingAuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum', 'track.activity'])->group(function () {
	// Auth routes
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::get('/me', [AuthController::class, 'me']);
	Route::post('/auth/logout', [PendingAuthController::class, 'logout']);
	Route::get('/auth/me', [PendingAuthController::class, 'me']);

	Route::get('/request-types', [RequestTypeController::class, 'index']);

	Route::prefix('my-requests')->group(function () {
		Route::get('/', [MyRequestController::class, 'index']);
		Route::post('/', [MyRequestController::class, 'store']);
		Route::get('/{serviceRequest}', [MyRequestController::class, 'show']);
		Route::delete('/{serviceRequest}', [MyRequestController::class, 'destroy']);
	});

	Route::middleware('role:admin')->group(function () {
		Route::get('/dashboard/stats', [DashboardStatsController::class, 'stats']);
		Route::get('/requests', [AdminPendingRequestController::class, 'index']);
		Route::get('/requests/{serviceRequest}', [
			AdminPendingRequestController::class,
			'show',
		]);
		Route::patch('/requests/{serviceRequest}/status', [
			AdminPendingRequestController::class,
			'updateStatus',
		]);
		Route::delete('/requests/{serviceRequest}', [
			AdminPendingRequestController::class,
			'destroy',
		]);
	});

	// User routes
	Route::prefix('users')->group(function () {
		Route::get('/', [UserController::class, 'index']);
		Route::post('/', [UserController::class, 'store'])->middleware('role:Admin,Staff');
		Route::get('/search', [UserController::class, 'search']);
		Route::get('/role/{role}', [UserController::class, 'getByRole']);
		Route::get('/status/{status}', [UserController::class, 'getByStatus']);					
		Route::get('/{id}', [UserController::class, 'show']);
		Route::put('/{id}', [UserController::class, 'update']);
		Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('role:Admin');
		Route::post('/{id}/assign-role', [UserController::class, 'assignRole'])->middleware('role:Admin');
		Route::post('/{id}/assign-status', [UserController::class, 'assignStatus'])->middleware('role:Admin,Staff');
		Route::post('/{id}/upload-image', [UserController::class, 'uploadImage']);
		Route::post('/{id}/add-note', [UserController::class, 'addNote'])->middleware('role:Admin,Staff');
	});

	// Leave routes
	Route::prefix('leaves')->group(function () {
		Route::get('/', [LeaveController::class, 'index']);
		Route::post('/', [LeaveController::class, 'store']);
		Route::get('/search', [LeaveController::class, 'search']);
		Route::get('/{id}', [LeaveController::class, 'show']);
		Route::put('/{id}', [LeaveController::class, 'update']);
		Route::delete('/{id}', [LeaveController::class, 'destroy']);
		Route::post('/{id}/approve', [LeaveController::class, 'approve'])->middleware('role:Admin,Staff');
		Route::post('/{id}/reject', [LeaveController::class, 'reject'])->middleware('role:Admin,Staff');
		Route::post('/{id}/add-note', [LeaveController::class, 'addNote']);
	});

	// Tool routes
	Route::prefix('tools')->group(function () {
		Route::get('/', [ToolController::class, 'index']);
		Route::post('/', [ToolController::class, 'store'])->middleware('role:Admin,Staff');
		Route::get('/search', [ToolController::class, 'search']);
		Route::get('/category/{category}', [ToolController::class, 'getByCategory']);
		Route::get('/status/{status}', [ToolController::class, 'getByStatus']);
		Route::get('/user/{userId}', [ToolController::class, 'getByUser']);
		Route::get('/{id}', [ToolController::class, 'show']);
		Route::put('/{id}', [ToolController::class, 'update'])->middleware('role:Admin,Staff');
		Route::delete('/{id}', [ToolController::class, 'destroy'])->middleware('role:Admin');
		Route::post('/{id}/assign-users', [ToolController::class, 'assignUsers'])->middleware('role:Admin,Staff');
		Route::post('/{id}/add-note', [ToolController::class, 'addNote'])->middleware('role:Admin,Staff');
		Route::post('/{id}/add-cost', [ToolController::class, 'addCost'])->middleware('role:Admin,Staff');
		Route::post('/{id}/add-billing', [ToolController::class, 'addBilling'])->middleware('role:Admin,Staff');
	});

	// Content routes
	Route::prefix('content')->group(function () {
		Route::get('/', [ContentController::class, 'index']);
		Route::post('/', [ContentController::class, 'store']);
		Route::get('/search', [ContentController::class, 'search']);
		Route::get('/user/{userId}', [ContentController::class, 'getByUser']);
		Route::get('/category/{categoryId}', [ContentController::class, 'getByCategory']);
		Route::get('/file-type/{fileType}', [ContentController::class, 'getByFileType']);
		Route::get('/{id}', [ContentController::class, 'show']);
		Route::get('/{id}/download', [ContentController::class, 'download']);
		Route::post('/{id}/share', [ContentController::class, 'share']);
		Route::delete('/{id}', [ContentController::class, 'destroy']);
	});

	Route::prefix('approvals')->group(function () {
		Route::get('/', [ApprovalController::class, 'index']);
		Route::post('/', [ApprovalController::class, 'store']);
		Route::get('/{approval}', [ApprovalController::class, 'show']);
		Route::put('/{approval}', [ApprovalController::class, 'update']);
		Route::post('/{approval}/approve', [ApprovalController::class, 'approve'])
			->middleware('role:admin,employee');
		Route::post('/{approval}/reject', [ApprovalController::class, 'reject'])
			->middleware('role:admin,employee');
	});

	Route::prefix('files')->group(function () {
		Route::get('/stats', [FileController::class, 'stats']);
		Route::get('/export', [FileController::class, 'export']);
		Route::get('/', [FileController::class, 'index']);
		Route::post('/', [FileController::class, 'store']);
		Route::patch('/{file}/notify', [FileController::class, 'updateNotify']);
		Route::patch('/{file}/archive', [FileController::class, 'updateArchive']);
		Route::get('/{file}', [FileController::class, 'show']);
		Route::delete('/{file}', [FileController::class, 'destroy']);
		Route::post('/{file}/share', [FileController::class, 'share']);
	});

	Route::prefix('work-tools')->group(function () {
		Route::get('/stats', [WorkToolController::class, 'stats']);
		Route::get('/', [WorkToolController::class, 'index']);
		Route::post('/', [WorkToolController::class, 'store']);
		Route::get('/{workTool}', [WorkToolController::class, 'show']);
		Route::put('/{workTool}', [WorkToolController::class, 'update']);
		Route::delete('/{workTool}', [WorkToolController::class, 'destroy']);
	});

	Route::prefix('accesses')->middleware('role:admin')->group(function () {
		Route::get('/stats', [AccessController::class, 'stats']);
		Route::get('/', [AccessController::class, 'index']);
		Route::post('/', [AccessController::class, 'store']);
		Route::get('/{access}', [AccessController::class, 'show']);
		Route::put('/{access}', [AccessController::class, 'update']);
		Route::delete('/{access}', [AccessController::class, 'destroy']);
		Route::post('/{access}/assign-users', [
			AccessController::class,
			'assignUsers',
		]);
	});

	Route::prefix('system-logs')->group(function () {
		Route::get('/stats', [SystemLogController::class, 'stats']);
		Route::get('/export', [SystemLogController::class, 'export']);
		Route::get('/', [SystemLogController::class, 'index']);
	});

	Route::prefix('notifications')->group(function () {
		Route::get('/', [NotificationController::class, 'index']);
		Route::post('/', [NotificationController::class, 'store'])
			->middleware('role:admin,employee');
		Route::post('/{notification}/read', [NotificationController::class, 'read']);
	});

	Route::get('/activities', [ActivityController::class, 'index'])
		->middleware('role:admin,employee');

	Route::prefix('clients')->group(function () {
		Route::get('/metrics', [ClientController::class, 'metrics']);
		Route::get('/export', [ClientController::class, 'export']);
		Route::get('/', [ClientController::class, 'index']);
		Route::post('/', [ClientController::class, 'store']);
		Route::get('/{client}/contracts', [ClientController::class, 'contracts']);
		Route::post('/{client}/contracts', [ClientController::class, 'storeContract']);
		Route::get('/{client}/employees', [ClientController::class, 'employees']);
		Route::post('/{client}/assign-employees', [
			ClientController::class,
			'assignEmployees',
		]);
		Route::get('/{client}', [ClientController::class, 'show']);
		Route::put('/{client}', [ClientController::class, 'update']);
		Route::delete('/{client}', [ClientController::class, 'destroy']);
	});

	Route::put('/contracts/{contract}', [ContractController::class, 'update']);
	Route::delete('/contracts/{contract}', [ContractController::class, 'destroy']);

	Route::prefix('employees')->group(function () {
		Route::get('/metrics', [EmployeeController::class, 'metrics']);
		Route::get('/export', [EmployeeController::class, 'export']);
		Route::get('/', [EmployeeController::class, 'index']);
		Route::post('/', [EmployeeController::class, 'store']);
		Route::get('/{employee}', [EmployeeController::class, 'show']);
		Route::put('/{employee}', [EmployeeController::class, 'update']);
		Route::delete('/{employee}', [EmployeeController::class, 'destroy']);
		Route::post(
			'/{employee}/assign-clients',
			[EmployeeController::class, 'assignClients']
		);
		Route::get('/{employee}/clients', [EmployeeController::class, 'clients']);
		Route::post('/{employee}/status', [EmployeeController::class, 'updateStatus']);
	});

	Route::get('/dashboard', [DashboardController::class, 'index']);
	Route::prefix('dashboard')->group(function () {
		Route::get('/', [DashboardController::class, 'index']);
	});

});

