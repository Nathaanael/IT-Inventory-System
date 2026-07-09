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
        Schema::create('data_switches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panel_id')->nullable()->constrained('panels')->nullOnDelete();
            $table->string('switch_id')->unique();
            $table->string('merk');
            $table->string('ip_address')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_switches');
    }
};
