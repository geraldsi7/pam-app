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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('registration_id')->constrained()->cascadeOnDelete();
            $table->string('method'); // paystack, bank_deposit
            $table->string('status')->default('pending'); // pending, success, failed
            $table->decimal('amount', 12, 2);
            $table->string('transaction_reference')->unique()->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
