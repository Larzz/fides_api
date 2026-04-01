<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('approvals', function (Blueprint $table) {
			$table->id();
			$table->enum('type', ['leave', 'wfh', 'ticket', 'tool', 'file']);
			$table->foreignId('user_id')->constrained()->cascadeOnDelete();
			$table->string('title');
			$table->text('description')->nullable();
			$table->json('metadata')->nullable();
			$table->enum('status', ['pending', 'approved', 'rejected'])
				->default('pending');
			$table->foreignId('approved_by')->nullable()->constrained('users')
				->nullOnDelete();
			$table->timestamp('approved_at')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('approvals');
	}
};
