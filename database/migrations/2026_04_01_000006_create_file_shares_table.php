<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('file_shares', function (Blueprint $table) {
			$table->id();
			$table->foreignId('file_id')->constrained('files')->cascadeOnDelete();
			$table->foreignId('shared_with_user_id')->nullable()->constrained('users')
				->cascadeOnDelete();
			$table->foreignId('shared_with_company_id')->nullable()
				->constrained('companies')->cascadeOnDelete();
			$table->boolean('notifications_enabled')->default(true);
			$table->timestamp('created_at')->useCurrent();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('file_shares');
	}
};
