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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('wishable_type');
            $table->unsignedBigInteger('wishable_id');
            $table->timestamps();

            // Indexes
            $table->index(['wishable_type', 'wishable_id']);
            $table->index('user_id');
            $table->unique(['user_id', 'wishable_type', 'wishable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
