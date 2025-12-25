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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('disk')->default('s3');
            $table->string('directory')->nullable();
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->unsigned()->nullable();
            $table->integer('width')->unsigned()->nullable();
            $table->integer('height')->unsigned()->nullable();
            $table->text('alt')->nullable();
            $table->json('focal_point')->nullable();
            $table->json('conversions')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('disk');
            $table->index('mime_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
