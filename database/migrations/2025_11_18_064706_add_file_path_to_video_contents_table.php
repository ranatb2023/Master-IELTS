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
        Schema::table('video_contents', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('url');
            $table->string('file_name')->nullable()->after('file_path');
            $table->string('file_type')->nullable()->after('file_name');
            $table->bigInteger('file_size')->nullable()->after('file_type'); // in bytes
            $table->integer('duration_seconds')->nullable()->after('file_size');
            $table->string('source')->default('url')->after('duration_seconds'); // 'url' or 'upload'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_contents', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name', 'file_type', 'file_size', 'duration_seconds', 'source']);
        });
    }
};
