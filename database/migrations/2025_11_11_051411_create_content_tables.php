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
        // Video Contents
        Schema::create('video_contents', function (Blueprint $table) {
            $table->id();
            $table->string('vimeo_id')->nullable();
            $table->string('url')->nullable();
            $table->json('captions')->nullable();
            $table->json('quality')->nullable();
            $table->longText('transcript')->nullable();
            $table->timestamps();

            $table->index('vimeo_id');
        });

        // Text Contents
        Schema::create('text_contents', function (Blueprint $table) {
            $table->id();
            $table->longText('body');
            $table->integer('reading_time')->nullable();
            $table->timestamps();
        });

        // Document Contents
        Schema::create('document_contents', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->integer('pages')->nullable();
            $table->timestamps();
        });

        // Audio Contents
        Schema::create('audio_contents', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->integer('duration_seconds')->nullable();
            $table->longText('transcript')->nullable();
            $table->timestamps();
        });

        // Presentation Contents
        Schema::create('presentation_contents', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->integer('slides')->nullable();
            $table->timestamps();
        });

        // Embed Contents
        Schema::create('embed_contents', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->nullable();
            $table->string('embed_url');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_contents');
        Schema::dropIfExists('text_contents');
        Schema::dropIfExists('document_contents');
        Schema::dropIfExists('audio_contents');
        Schema::dropIfExists('presentation_contents');
        Schema::dropIfExists('embed_contents');
    }
};
