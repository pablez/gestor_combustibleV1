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
        Schema::table('unidad_organizacionals', function (Blueprint $table) {
            // Agregar la columna estado como enum
            $table->enum('estado', ['Activa', 'Inactiva'])->default('Activa')->after('activa');
            
            // Migrar datos existentes: convertir boolean 'activa' a enum 'estado'
            // Esto se hará en un paso separado después de agregar la columna
        });

        // Migrar los datos existentes
        DB::statement("
            UPDATE unidad_organizacionals 
            SET estado = CASE 
                WHEN activa = 1 THEN 'Activa'
                WHEN activa = 0 THEN 'Inactiva'
                ELSE 'Activa'
            END
        ");

        // Opcional: Eliminar la columna 'activa' si ya no se necesita
        // Descomenta la siguiente línea si quieres eliminar la columna booleana
        // Schema::table('unidad_organizacionals', function (Blueprint $table) {
        //     $table->dropColumn('activa');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unidad_organizacionals', function (Blueprint $table) {
            // Eliminar la columna estado
            $table->dropColumn('estado');
        });
        
        // Si eliminaste la columna 'activa', descomenta estas líneas para restaurarla
        // Schema::table('unidad_organizacionals', function (Blueprint $table) {
        //     $table->boolean('activa')->default(true)->after('nombre_unidad');
        // });
    }
};
