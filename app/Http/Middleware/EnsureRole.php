<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next, string ...$roles): Response
	{
		if (!$request->user()) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthenticated',
			], 401);
		}

		$userRole = strtolower((string) $request->user()->role);
		$requiredRoles = array_map('strtolower', $roles);

		if (!in_array($userRole, $requiredRoles, true)) {
			return response()->json([
				'success' => false,
				'message' => 'Insufficient permissions',
			], 403);
		}

		return $next($request);
	}
}

