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
        Schema::create('user_approval_requests', function (Blueprint $table) {
            $table->id();
            
            // Relaciones principales
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade'); // ID del usuario a aprobar
            $table->foreignId('creado_por')->constrained('users'); // Quién creó la solicitud
            $table->foreignId('supervisor_asignado_id')->nullable()->constrained('users'); // Supervisor operacional
            
            // Información de la solicitud
            $table->enum('tipo_solicitud', ['nuevo_usuario', 'cambio_rol', 'reactivacion'])->default('nuevo_usuario');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->string('rol_solicitado')->nullable();
            $table->text('razon')->nullable();
            
            // Información de aprobación/rechazo
            $table->foreignId('aprobado_por')->nullable()->constrained('users');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->text('razon_rechazo')->nullable();
            $table->text('comentarios_aprobacion')->nullable();
            
            // Metadatos para reportes
            $table->string('rol_creador')->nullable();
            $table->string('rol_aprobador')->nullable();
            $table->foreignId('unidad_organizacional_id')->nullable()->constrained('unidad_organizacionals', 'id_unidad_organizacional');
            
            // Datos adicionales
            $table->json('datos_usuario')->nullable();
            $table->json('metadatos_aprobacion')->nullable();
            
            $table->timestamps();
            
            // Índices para reportes
            $table->index(['estado', 'created_at']);
            $table->index(['rol_creador', 'estado']);
            $table->index(['rol_aprobador', 'fecha_aprobacion']);
            $table->index(['unidad_organizacional_id', 'estado']);
            $table->index(['creado_por', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_approval_requests');
    }
};
