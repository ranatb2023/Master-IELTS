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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->enum('type', ['mcq_single', 'mcq_multiple', 'true_false', 'short_answer', 'passage_mcq']);
            $table->text('question');
            $table->text('description')->nullable();
            $table->decimal('points', 8, 2)->default(1.00);
            $table->integer('order')->default(0);
            $table->enum('media_type', ['none', 'image', 'audio', 'video'])->default('none');
            $table->string('media_url')->nullable();
            $table->text('explanation')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->timestamps();

            // Indexes
            $table->index('quiz_id');
            $table->index('type');
            $table->index('difficulty');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
