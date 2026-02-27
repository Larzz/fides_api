<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\ToolController;
use App\Http\Controllers\Api\ContentController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
	// Auth routes
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::get('/me', [AuthController::class, 'me']);

	// User routes
	Route::prefix('users')->group(function () {
		Route::get('/', [UserController::class, 'index']);
		Route::post('/', [UserController::class, 'store'])->middleware('role:Admin,Manager');
		Route::get('/search', [UserController::class, 'search']);
		Route::get('/role/{role}', [UserController::class, 'getByRole']);
		Route::get('/status/{status}', [UserController::class, 'getByStatus']);
		Route::get('/{id}', [UserController::class, 'show']);
		Route::put('/{id}', [UserController::class, 'update']);
		Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('role:Admin');
		Route::post('/{id}/assign-role', [UserController::class, 'assignRole'])->middleware('role:Admin');
		Route::post('/{id}/assign-status', [UserController::class, 'assignStatus'])->middleware('role:Admin,Manager');
		Route::post('/{id}/upload-image', [UserController::class, 'uploadImage']);
		Route::post('/{id}/add-note', [UserController::class, 'addNote'])->middleware('role:Admin,Manager');
	});

	// Leave routes
	Route::prefix('leaves')->group(function () {
		Route::get('/', [LeaveController::class, 'index']);
		Route::post('/', [LeaveController::class, 'store']);
		Route::get('/search', [LeaveController::class, 'search']);
		Route::get('/{id}', [LeaveController::class, 'show']);
		Route::put('/{id}', [LeaveController::class, 'update']);
		Route::delete('/{id}', [LeaveController::class, 'destroy']);
		Route::post('/{id}/approve', [LeaveController::class, 'approve'])->middleware('role:Admin,Manager');
		Route::post('/{id}/reject', [LeaveController::class, 'reject'])->middleware('role:Admin,Manager');
		Route::post('/{id}/add-note', [LeaveController::class, 'addNote']);
	});

	// Tool routes
	Route::prefix('tools')->group(function () {
		Route::get('/', [ToolController::class, 'index']);
		Route::post('/', [ToolController::class, 'store'])->middleware('role:Admin,Manager');
		Route::get('/search', [ToolController::class, 'search']);
		Route::get('/category/{category}', [ToolController::class, 'getByCategory']);
		Route::get('/status/{status}', [ToolController::class, 'getByStatus']);
		Route::get('/user/{userId}', [ToolController::class, 'getByUser']);
		Route::get('/{id}', [ToolController::class, 'show']);
		Route::put('/{id}', [ToolController::class, 'update'])->middleware('role:Admin,Manager');
		Route::delete('/{id}', [ToolController::class, 'destroy'])->middleware('role:Admin');
		Route::post('/{id}/assign-users', [ToolController::class, 'assignUsers'])->middleware('role:Admin,Manager');
		Route::post('/{id}/add-note', [ToolController::class, 'addNote'])->middleware('role:Admin,Manager');
		Route::post('/{id}/add-cost', [ToolController::class, 'addCost'])->middleware('role:Admin,Manager');
		Route::post('/{id}/add-billing', [ToolController::class, 'addBilling'])->middleware('role:Admin,Manager');
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
});

