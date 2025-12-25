<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'suspended' to enrollment status enum
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('active', 'completed', 'expired', 'canceled', 'suspended') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values (remove 'suspended')
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('active', 'completed', 'expired', 'canceled') DEFAULT 'active'");
    }
};
