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
        Schema::create('subscription_plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->onDelete('cascade');
            $table->string('feature_key');
            $table->foreign('feature_key')
                ->references('feature_key')
                ->on('package_features')
                ->onDelete('cascade');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            // Unique constraint: one feature per plan
            $table->unique(['subscription_plan_id', 'feature_key'], 'unique_plan_feature');

            // Indexes for performance
            $table->index('subscription_plan_id');
            $table->index('feature_key');
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_features');
    }
};
