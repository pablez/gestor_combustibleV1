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
        Schema::create('consumo_combustibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_transporte_id')->constrained('unidad_transportes')->onDelete('cascade');
            $table->foreignId('conductor_id')->constrained('users')->onDelete('cascade'); // FK al usuario conductor
            $table->date('fecha_registro');
            $table->decimal('kilometraje_inicio', 10, 2);
            $table->decimal('kilometraje_fin', 10, 2);
            $table->decimal('litros_consumidos', 8, 2);
            $table->decimal('costo_bs', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps(); // Coincide con fecha_creacion y fecha_actualizacion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumo_combustibles');
    }
};