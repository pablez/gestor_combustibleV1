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
        Schema::create('unidad_transportes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_unidad', 100);
            $table->string('placa_identificador', 50)->unique();
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->integer('anio');
            $table->enum('tipo_combustible', ['Gasolina', 'Diesel', 'GNV', 'Electrico', 'Otros']);
            $table->decimal('capacidad_tanque_litros', 8, 2);
            $table->enum('estado_operativo', ['Operativo', 'En Mantenimiento', 'Fuera de Servicio'])->default('Operativo');
            $table->decimal('kilometraje_actual', 10, 2)->default(0.00);
            $table->timestamps(); // Coincide con fecha_registro y fecha_actualizacion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_transportes');
    }
};
