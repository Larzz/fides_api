<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Tool;
use App\Models\UserContentUpload;
use App\Models\UserContentDownload;
use App\Models\UserContentShare;
use App\Models\Leave;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    //

    public function index(Request $request): JsonResponse
    {
        return ApiResponse::success([
            'message' => 'Dashboard data',
        ]);
    }


    public function getStatistics(Request $request): JsonResponse 
    {
        $statistics = [
            'total_uploaded_content' => UserContentUpload::count()->where('user_id', auth()->user()->id),
            'total_downloaded_content' => UserContentDownload::count()->where('user_id', auth()->user()->id),
            'total_shared_content' => UserContentShare::count()->where('user_id', auth()->user()->id),
            'total_users_online' => User::where('last_login_at', '>=', now()->startOfDay())->count(),
            'total_login_todays' => User::where('last_login_at', '>=', now()->startOfDay())->count(),
        ];
        return ApiResponse::success($statistics, 'Dashboard statistics');
    }

    public function getAllNotifications(Request $request): JsonResponse
    {
        $notifications = Notification::all();
        return ApiResponse::paginated(
            NotificationResource::collection($notifications),
            'All notifications'
        );
    }
