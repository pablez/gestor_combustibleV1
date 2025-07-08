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
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_transporte_id')->constrained('unidad_transportes')->onDelete('cascade');
            $table->date('fecha_mantenimiento');
            $table->enum('tipo_mantenimiento', ['Preventivo', 'Correctivo']);
            $table->text('descripcion_trabajo')->nullable();
            $table->decimal('costo_bs', 10, 2)->nullable();
            $table->decimal('kilometraje_mantenimiento', 10, 2)->nullable();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedors')->onDelete('set null'); // Optional
            $table->date('fecha_proximo_mantenimiento')->nullable(); // Optional
            $table->timestamps(); // Coincide con fecha_creacion y fecha_actualizacion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};