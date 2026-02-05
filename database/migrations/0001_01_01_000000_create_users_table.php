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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role');
            $table->string('status');
            $table->string('notes');
            $table->string('image');
            $table->string('resume');
            $table->string('cover_letter');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('user_system_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity');
            $table->string('description');
            $table->string('user_id');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_phone');
        });

        Schema::create('user_system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notification');
            $table->string('description');
            $table->string('user_id');
            table->string('remarks');
            $table->timestamps();
        });

        Schema::create('user_system_notifications_read', function (Blueprint $table) {
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

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('user_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('user_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note');
            $table->timestamps();
        });

        Schema::create('user_images', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->timestamps();
        });

        Schema::create('user_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('activity');
            $table->string('description');
            $table->string('role_id');
            $table->string('role_name');
            $table->string('role_description');
            $table->string('status_id');
            $table->string('status_name');
            $table->string('status_description');
            $table->string('note_id');
            $table->string('note_note');
            $table->string('image_id');
            $table->string('image_image');
            $table->timestamps();
        });

        Schema::create('user_content_upload', function(Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('file_size');
            $table->string('file_mime_type');
            $table->string('file_extension');
            $table->string('file_hash');
            $table->string('file_hash_type');
            $table->string('file_hash_algorithm');
            $table->string('file_hash_algorithm_name');
            $table->string('file_hash_algorithm_description');
            $table->string('file_hash_algorithm_version');
            $table->string('file_hash_algorithm_version_name');
            $table->string('file_hash_algorithm_version_description');
            $table->string('file_hash_algorithm_version_version');
            $table->string('category_id');
            $table->string('notification_id');
            $table->timestamps();
        });

        Schema::create('user_content_share', function(Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('file_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('file_size');
        });

        Schema::create('user_content_category', function(Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->string('category_description');
            $table->timestamps();
        });

        Schema::create('user_content_download', function(Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('file_size');
            $table->string('file_mime_type');
            $table->string('file_extension');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
