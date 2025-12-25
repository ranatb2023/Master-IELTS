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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('course_updates')->default(true);
            $table->boolean('assignment_reminders')->default(true);
            $table->boolean('message_notifications')->default(true);
            $table->boolean('marketing_emails')->default(false);
            $table->boolean('weekly_digest')->default(true);
            $table->enum('theme', ['light', 'dark', 'auto'])->default('light');
            $table->json('notifications_settings')->nullable();
            $table->json('privacy_settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
