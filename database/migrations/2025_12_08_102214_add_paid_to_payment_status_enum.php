<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the payment_status enum to include 'paid'
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN payment_status ENUM('pending', 'completed', 'failed', 'refunded', 'free', 'paid') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN payment_status ENUM('pending', 'completed', 'failed', 'refunded', 'free') DEFAULT 'pending'");
    }
};
