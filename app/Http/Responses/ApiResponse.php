<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiResponse
{
	/**
	 * Success response
	 */
	public static function success(mixed $data = null, string $message = 'Success', int $statusCode = Response::HTTP_OK, ?array $meta = null): JsonResponse
	{
		$response = [
			'success' => true,
			'message' => $message,
		];

		if ($data !== null) {
			$response['data'] = $data;
		}

		if ($meta !== null) {
			$response['meta'] = $meta;
		}

		return response()->json($response, $statusCode);
	}

	/**
	 * Error response
	 */
	public static function error(string $message = 'Error', int $statusCode = Response::HTTP_BAD_REQUEST, ?array $errors = null): JsonResponse
	{
		$response = [
			'success' => false,
			'message' => $message,
		];

		if ($errors !== null) {
			$response['errors'] = $errors;
		}

		return response()->json($response, $statusCode);
	}

	/**
	 * Created response
	 */
	public static function created(mixed $data = null, string $message = 'Resource created successfully'): JsonResponse
	{
		return self::success($data, $message, Response::HTTP_CREATED);
	}

	/**
	 * No content response
	 */
	public static function noContent(string $message = 'Resource deleted successfully'): JsonResponse
	{
		return self::success(null, $message, Response::HTTP_NO_CONTENT);
	}

	/**
	 * Paginated response
	 */
	public static function paginated(mixed $data, string $message = 'Success'): JsonResponse
	{
		return self::success($data, $message, Response::HTTP_OK, [
			'pagination' => [
				'current_page' => $data->currentPage(),
				'per_page' => $data->perPage(),
				'total' => $data->total(),
				'last_page' => $data->lastPage(),
				'from' => $data->firstItem(),
				'to' => $data->lastItem(),
			],
		]);
	}
}

