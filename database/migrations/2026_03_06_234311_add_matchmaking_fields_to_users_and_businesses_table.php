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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('registration_id')->nullable()->constrained('registrations')->nullOnDelete();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->boolean('is_public')->default(true);
            $table->json('matchmaking_interests')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
            $table->dropColumn('registration_id');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'matchmaking_interests']);
        });
    }
};
