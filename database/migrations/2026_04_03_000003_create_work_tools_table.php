<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('work_tools', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('category')->index();
			$table->enum('billing_type', ['monthly', 'annual', 'free'])->index();
			$table->decimal('cost', 12, 2)->default(0);
			$table->string('currency', 3)->default('AED');
			$table->date('renewal_date')->nullable()->index();
			$table->enum('status', ['active', 'inactive'])->default('active')->index();
			$table->text('notes')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('work_tools');
	}
};
