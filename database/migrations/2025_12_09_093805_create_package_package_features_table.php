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
        Schema::create('package_package_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')
                ->constrained('packages')
                ->onDelete('cascade');
            $table->string('feature_key');
            $table->foreign('feature_key')
                ->references('feature_key')
                ->on('package_features')
                ->onDelete('cascade');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            // Unique constraint: one feature per package
            $table->unique(['package_id', 'feature_key'], 'unique_package_feature');

            // Indexes for performance
            $table->index('package_id');
            $table->index('feature_key');
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_package_features');
    }
};
