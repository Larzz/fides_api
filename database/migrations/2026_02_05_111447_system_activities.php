<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity');
            $table->string('description');
            $table->string('user_id');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_phone');
        });

        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification');
            $table->string('description');
            $table->string('user_id');
            table->string('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
