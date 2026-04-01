<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('contracts', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->cascadeOnDelete();
			$table->string('title');
			$table->date('start_date');
			$table->date('end_date');
			$table->enum('status', ['active', 'expiring', 'expired'])
				->default('active');
			$table->timestamps();

			$table->index(['company_id', 'end_date']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('contracts');
	}
};
