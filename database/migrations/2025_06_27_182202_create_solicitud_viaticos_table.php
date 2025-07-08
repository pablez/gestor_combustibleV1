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
        Schema::create('solicitud_viaticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // usuario_id
            $table->dateTime('fecha_solicitud');
            $table->string('destino', 255);
            $table->text('proposito_mision')->nullable();
            $table->date('fecha_inicio_mision');
            $table->date('fecha_fin_mision');
            $table->integer('dias_solicitados');
            $table->decimal('monto_solicitado_bs', 10, 2);
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade'); // FK al usuario supervisor
            $table->enum('estado_solicitud', ['Pendiente', 'Aprobada', 'Rechazada', 'Liquidada'])->default('Pendiente');
            $table->dateTime('fecha_aprobacion_rechazo')->nullable();
            $table->text('justificacion_rechazo')->nullable();
            $table->timestamps(); // Coincide con fecha_creacion y fecha_actualizacion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_viaticos');
    }
};