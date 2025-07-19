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
            $table->unsignedBigInteger('unidad_organizacional_id')->nullable()->after('supervisor_id');
            
            // Crear la relación de clave foránea
            $table->foreign('unidad_organizacional_id')
                  ->references('id_unidad_organizacional')
                  ->on('unidad_organizacionals')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unidad_organizacional_id']);
            $table->dropColumn('unidad_organizacional_id');
        });
    }
};
