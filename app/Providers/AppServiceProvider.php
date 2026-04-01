<?php

namespace App\Providers;

use App\Events\LeaveStatusChanged;
use App\Events\ToolStatusChanged;
use App\Listeners\HandleLeaveStatusChanged;
use App\Listeners\HandleToolStatusChanged;
use App\Models\Access;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ServiceRequest;
use App\Models\SystemLog;
use App\Models\User;
use App\Models\WorkTool;
use App\Policies\AccessPolicy;
use App\Policies\ClientPolicy;
use App\Policies\ContractPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\ServiceRequestPolicy;
use App\Policies\SystemLogPolicy;
use App\Policies\WorkToolPolicy;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
		Gate::policy(User::class, EmployeePolicy::class);
		Gate::policy(Company::class, ClientPolicy::class);
		Gate::policy(Contract::class, ContractPolicy::class);
		Gate::policy(ServiceRequest::class, ServiceRequestPolicy::class);
		Gate::policy(WorkTool::class, WorkToolPolicy::class);
		Gate::policy(Access::class, AccessPolicy::class);
		Gate::policy(SystemLog::class, SystemLogPolicy::class);
	}
}
