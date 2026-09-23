<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('mfa_secret')->nullable()->after('vault_pin');
            $table->timestamp('mfa_enabled_at')->nullable()->after('mfa_secret');
            $table->unsignedBigInteger('mfa_last_used_at')->nullable()->after('mfa_enabled_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mfa_secret', 'mfa_enabled_at', 'mfa_last_used_at']);
        });
    }
};
