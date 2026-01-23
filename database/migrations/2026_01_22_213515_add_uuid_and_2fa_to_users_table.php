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
            $table->uuid('uuid')->unique()->after('id');
            $table->timestamp('two_factor_enabled_at')->nullable()->after('password');
            $table->text('two_factor_secret')->nullable()->after('two_factor_enabled_at');
            $table->json('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'two_factor_enabled_at', 'two_factor_secret', 'two_factor_recovery_codes']);
        });
    }
};
