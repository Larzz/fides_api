<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('file_shares', function (Blueprint $table) {
			$table->boolean('share_all_employees')->default(false)
				->after('file_id');
			$table->index(['file_id', 'share_all_employees']);
		});
	}

	public function down(): void
	{
		Schema::table('file_shares', function (Blueprint $table) {
			$table->dropIndex(['file_id', 'share_all_employees']);
			$table->dropColumn('share_all_employees');
		});
	}
};
