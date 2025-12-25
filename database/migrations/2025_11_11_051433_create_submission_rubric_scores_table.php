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
        Schema::create('submission_rubric_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('assignment_submissions')->cascadeOnDelete();
            $table->foreignId('rubric_id')->constrained('assignment_rubrics')->cascadeOnDelete();
            $table->decimal('points', 10, 2);
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index('submission_id');
            $table->index('rubric_id');
            $table->unique(['submission_id', 'rubric_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_rubric_scores');
    }
};
