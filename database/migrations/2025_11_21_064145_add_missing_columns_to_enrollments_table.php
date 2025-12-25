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
        Schema::table('enrollments', function (Blueprint $table) {
            // Payment related columns
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded', 'free'])
                  ->default('pending')
                  ->after('status');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_status');

            // Refund related columns
            $table->string('refund_reason')->nullable()->after('amount_paid');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('refund_reason');
            $table->timestamp('refunded_at')->nullable()->after('refund_amount');

            // Enrollment source tracking
            $table->enum('enrollment_source', ['manual', 'self', 'package', 'admin', 'import'])
                  ->default('manual')
                  ->after('refunded_at');

            // Notes for admin
            $table->text('notes')->nullable()->after('enrollment_source');
        });

        // Update the status enum to include 'completed' and 'suspended'
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('active', 'completed', 'expired', 'canceled', 'suspended') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'amount_paid',
                'refund_reason',
                'refund_amount',
                'refunded_at',
                'enrollment_source',
                'notes'
            ]);
        });

        // Revert status enum to original values
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('active', 'expired', 'canceled') DEFAULT 'active'");
    }
};
