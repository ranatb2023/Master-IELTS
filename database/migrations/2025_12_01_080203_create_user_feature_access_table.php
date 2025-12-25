<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_feature_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->onDelete('cascade');
            $table->string('feature_key');
            $table->boolean('has_access')->default(true);
            $table->datetime('access_granted_at');
            $table->datetime('access_expires_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'feature_key']);
            $table->index('package_id');
            $table->index('subscription_id');
            $table->index('has_access');
            $table->index('access_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_feature_accesses');
    }
};
