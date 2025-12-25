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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->string('title');
            $table->longText('description');
            $table->text('instructions')->nullable();
            $table->decimal('max_points', 10, 2)->default(100.00);
            $table->decimal('passing_points', 10, 2)->default(70.00);
            $table->timestamp('due_date')->nullable();
            $table->boolean('allow_late_submission')->default(false);
            $table->decimal('late_penalty', 5, 2)->default(0.00);
            $table->integer('max_file_size')->default(10);
            $table->json('allowed_file_types')->nullable();
            $table->integer('max_files')->default(5);
            $table->boolean('auto_grade')->default(false);
            $table->boolean('require_passing')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('topic_id');
            $table->index(['topic_id', 'order']);
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
