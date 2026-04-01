<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table name `requests` per product spec (model: App\Models\ServiceRequest).
 */
return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('requests', function (Blueprint $table) {
			$table->id();
			$table->foreignId('request_type_id')
				->constrained('request_types')
				->cascadeOnDelete();
			$table->foreignId('user_id')
				->constrained('users')
				->cascadeOnDelete();
			$table->text('details');
			$table->enum('status', [
				'pending',
				'reviewed',
				'reimbursed',
				'rejected',
				'action_required',
			])->default('pending');
			$table->timestamp('submitted_at');
			$table->foreignId('reviewed_by')
				->nullable()
				->constrained('users')
				->nullOnDelete();
			$table->timestamp('reviewed_at')->nullable();
			$table->text('notes')->nullable();
			$table->timestamps();

			$table->index('status');
			$table->index('user_id');
			$table->index('request_type_id');
			$table->index('submitted_at');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('requests');
	}
};
