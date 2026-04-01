<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('users', function (Blueprint $table) {
			$table->string('job_title')->nullable()->after('role');
			$table->timestamp('last_active_at')->nullable()->after('status');
			$table->index(['status', 'role']);
		});
	}

	public function down(): void
	{
		Schema::table('users', function (Blueprint $table) {
			$table->dropIndex(['status', 'role']);
			$table->dropColumn(['job_title', 'last_active_at']);
		});
	}
};
