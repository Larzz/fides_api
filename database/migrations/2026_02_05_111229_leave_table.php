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
        //
        Schema::create('leave', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('user_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status');
            $table->string('type');
            $table->string('reason');
            $table->string('notes');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('leave_system_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity');
            $table->string('description');
            $table->string('leave_id');
            $table->string('leave_name');
        });

        Schema::create('leave_system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification');
            $table->string('description');
            $table->string('user_id');
            $table->string('remarks');
        });

        Schema::create('leave_system_notifications_read', function (Blueprint $table) {
            $table->id();
            $table->string('notification_id');
            $table->string('user_id');
            $table->string('read_at');
            $table->string('read_by');
            $table->string('read_by_name');
            $table->string('read_by_email');
            $table->string('read_by_phone');
            $table->string('read_by_address');
            $table->string('read_by_city');
            $table->string('read_by_state');
            $table->timestamps();
        });

        Schema::create('leave_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });
        
        Schema::create('leave_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('leave');
        Schema::dropIfExists('leave_system_activities');
        Schema::dropIfExists('leave_system_notifications');
        Schema::dropIfExists('leave_system_notifications_read');
        Schema::dropIfExists('leave_statuses');
        Schema::dropIfExists('leave_notes');
    }
};
