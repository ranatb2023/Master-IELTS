<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enrollment_source enum to include 'subscription'
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN enrollment_source ENUM('manual', 'self', 'package', 'admin', 'import', 'subscription') DEFAULT 'manual'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN enrollment_source ENUM('manual', 'self', 'package', 'admin', 'import') DEFAULT 'manual'");
    }
};
