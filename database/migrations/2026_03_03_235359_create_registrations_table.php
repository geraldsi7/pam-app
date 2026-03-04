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
        Schema::create('registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_code')->unique();
            $table->string('email')->unique();
            $table->json('personal_info')->nullable();
            $table->integer('current_step')->default(0);
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->string('referral_code')->nullable();
            $table->foreignUuid('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->boolean('addon_expo')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
