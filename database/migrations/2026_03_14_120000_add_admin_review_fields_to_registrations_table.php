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
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('admin_review_status')->default('pending')->after('payment_method');
            $table->text('admin_review_notes')->nullable()->after('admin_review_status');
            $table->timestamp('admin_reviewed_at')->nullable()->after('admin_review_notes');
            $table->foreignUuid('admin_reviewed_by')->nullable()->after('admin_reviewed_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['admin_reviewed_by']);
            $table->dropColumn([
                'admin_review_status',
                'admin_review_notes',
                'admin_reviewed_at',
                'admin_reviewed_by',
            ]);
        });
    }
};
