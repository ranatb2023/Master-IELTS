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
        Schema::create('question_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique()->comment('Unique identifier for question type (e.g., true_false, mcq_single)');
            $table->string('name', 100)->comment('Human-readable name');
            $table->text('description')->nullable()->comment('Optional description of question type');
            $table->longText('input_schema')->nullable()->comment('JSON schema describing form fields for creating questions');
            $table->longText('output_schema')->nullable()->comment('JSON schema describing how answers are stored');
            $table->enum('scoring_strategy', ['auto_exact', 'auto_partial', 'manual'])->default('manual')->comment('How this question type should be graded');
            $table->boolean('is_active')->default(true)->comment('Whether this question type is available for use');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_types');
    }
};
