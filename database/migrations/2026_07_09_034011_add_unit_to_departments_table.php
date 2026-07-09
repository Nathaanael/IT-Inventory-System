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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('unit')->after('name')->nullable();
            
            // Drop old unique constraint and add new composite one
            $table->dropUnique(['name']);
            $table->unique(['name', 'unit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropUnique(['name', 'unit']);
            $table->unique('name');
            
            $table->dropColumn('unit');
        });
    }
};
