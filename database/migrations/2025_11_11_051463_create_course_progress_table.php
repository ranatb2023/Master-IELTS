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
        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->decimal('progress_percentage', 5, 2)->default(0.00);
            $table->integer('completed_lessons')->default(0);
            $table->integer('total_lessons')->nullable();
            $table->integer('completed_quizzes')->default(0);
            $table->integer('total_quizzes')->nullable();
            $table->integer('completed_assignments')->default(0);
            $table->integer('total_assignments')->nullable();
            $table->decimal('average_quiz_score', 5, 2)->nullable();
            $table->decimal('average_assignment_score', 5, 2)->nullable();
            $table->integer('total_time_spent')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->unique(['user_id', 'course_id']);
            $table->index('progress_percentage');
            $table->index('last_accessed_at');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_progress');
    }
};
