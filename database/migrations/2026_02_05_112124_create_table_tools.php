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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('image');
            $table->string('url');
            $table->string('category');
            $table->string('subcategory');
            $table->string('tags');
            $table->string('status');
            $table->string('notes');
            $table->string('user_id');
            $table->timestamps();
        });

        Schema::create('tool_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('tool_billings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_cost', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_system_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity');
            $table->string('description');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification');
            $table->string('description');  
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_system_notifications_read', function (Blueprint $table) {
            $table->id();
            $table->string('notification_id');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note');
            $table->string('tool_id');
            $table->timestamps();
        });

        Schema::create('tool_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('tool_id');
            $table->timestamps();
        });
        
        Schema::create('tool_system_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity');
            $table->string('description');
            $table->string('tool_id');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
