<?php

namespace App\Http\Middleware;

use App\Models\Activity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
	public function handle(Request $request, Closure $next): Response
	{
		$user = $request->user();
		if ($user) {
			$user->forceFill(['last_active_at' => now()])->save();

			if (!$request->isMethod('get')) {
				Activity::create([
					'user_id' => $user->id,
					'action' => 'api_activity',
					'description' => sprintf(
						'%s %s',
						$request->method(),
						$request->path()
					),
					'ip_address' => $request->ip(),
					'created_at' => now(),
				]);
			}
		}

		return $next($request);
	}
}
