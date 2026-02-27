<?php

namespace App\Providers;

use App\Events\LeaveStatusChanged;
use App\Events\ToolStatusChanged;
use App\Listeners\HandleLeaveStatusChanged;
use App\Listeners\HandleToolStatusChanged;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array<class-string, array<int, class-string>>
	 */
	protected $listen = [
		LeaveStatusChanged::class => [
			HandleLeaveStatusChanged::class,
		],
		ToolStatusChanged::class => [
			HandleToolStatusChanged::class,
		],
	];

	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		//
	}
}
