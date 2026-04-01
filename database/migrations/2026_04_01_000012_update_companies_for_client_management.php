<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('companies', function (Blueprint $table) {
			$table->string('primary_contact_name')->nullable()->after('name');
			$table->string('primary_contact_email')->nullable()->after('primary_contact_name');
			$table->string('logo')->nullable()->after('primary_contact_email');
			$table->enum('status', ['active', 'inactive'])->default('active')->after('logo');
			$table->index('status');
		});
	}

	public function down(): void
	{
		Schema::table('companies', function (Blueprint $table) {
			$table->dropIndex(['status']);
			$table->dropColumn([
				'primary_contact_name',
				'primary_contact_email',
				'logo',
				'status',
			]);
		});
	}
};
