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
        Schema::create('liquidacion_viaticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_viatico_id')->unique()->constrained('solicitud_viaticos')->onDelete('cascade');
            $table->dateTime('fecha_liquidacion');
            $table->decimal('monto_liquidado_bs', 10, 2);
            $table->decimal('monto_devuelto_bs', 10, 2)->nullable();
            $table->decimal('monto_a_reembolsar_bs', 10, 2)->nullable();
            $table->boolean('comprobantes_adjuntos')->default(false);
            $table->timestamps(); // Coincide con fecha_creacion y fecha_actualizacion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liquidacion_viaticos');
    }
};