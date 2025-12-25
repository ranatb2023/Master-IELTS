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
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('progressable_type');
            $table->unsignedBigInteger('progressable_id');
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent')->default(0);
            $table->decimal('score', 8, 2)->nullable();
            $table->string('last_position')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Polymorphic index
            $table->index(['progressable_type', 'progressable_id']);
            // Composite index for user and progressable
            $table->index(['user_id', 'progressable_type', 'progressable_id']);
            // Status index
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};
