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
        Schema::table('businesses', function (Blueprint $table) {
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('attendance_mode', ['in_person', 'online']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['country_id', 'email', 'phone', 'attendance_mode']);
        });
    }
};
