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
        Schema::create('solicitud_combustibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // usuario_id
            $table->foreignId('unidad_transporte_id')->constrained('unidad_transportes')->onDelete('cascade');
            $table->dateTime('fecha_solicitud');
            $table->decimal('cantidad_solicitada_litros', 8, 2);
            $table->string('destino', 255);
            $table->text('actividades_realizar')->nullable();
            $table->time('hora_salida_estimada')->nullable();
            $table->time('hora_llegada_estimada')->nullable();
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade'); // FK al usuario supervisor
            $table->enum('estado_solicitud', ['Pendiente', 'Aprobada', 'Rechazada', 'Despachada'])->default('Pendiente');
            $table->dateTime('fecha_aprobacion_rechazo')->nullable();
            $table->text('justificacion_rechazo')->nullable();
            $table->string('codigo_vale', 100)->unique()->nullable(); // Hacemos nullable por si se genera después
            $table->timestamps(); // Coincide con fecha_creacion y fecha_actualizacion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_combustibles');
    }
};