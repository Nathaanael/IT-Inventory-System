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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perangkat');
            $table->string('lokasi');
            $table->string('ssid_wifi');
            $table->text('password_wifi'); // Menggunakan text karena enkripsi Crypt cukup panjang
            $table->float('threshold_suhu')->default(30.0);
            $table->text('keterangan')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('status')->default('Offline');
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
