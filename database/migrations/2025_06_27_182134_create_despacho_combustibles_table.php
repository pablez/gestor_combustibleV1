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
        Schema::create('despacho_combustibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_combustible_id')->unique()->constrained('solicitud_combustibles')->onDelete('cascade');
            $table->dateTime('fecha_despacho');
            $table->decimal('cantidad_despachada_litros', 8, 2);
            $table->decimal('kilometraje_al_despacho', 10, 2);
            $table->foreignId('proveedor_id')->constrained('proveedors')->onDelete('cascade');
            $table->string('numero_vale_fisico', 100)->unique();
            $table->decimal('costo_total_bs', 10, 2);
            $table->timestamps(); // Coincide con fecha_creacion y fecha_actualizacion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despacho_combustibles');
    }
};