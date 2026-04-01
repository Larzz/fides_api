<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('files', function (Blueprint $table) {
			$table->string('category')->nullable()->after('title');
			$table->timestamp('archived_at')->nullable()->after('updated_at');
			$table->boolean('notify_stakeholders')->default(true)->after('archived_at');
			$table->index('category');
			$table->index('archived_at');
		});
	}

	public function down(): void
	{
		Schema::table('files', function (Blueprint $table) {
			$table->dropIndex(['category']);
			$table->dropIndex(['archived_at']);
			$table->dropColumn(['category', 'archived_at', 'notify_stakeholders']);
		});
	}
};
