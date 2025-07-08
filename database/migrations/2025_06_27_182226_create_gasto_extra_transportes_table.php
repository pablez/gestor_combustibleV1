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
        Schema::create('gasto_extra_transportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // usuario_id
            $table->foreignId('unidad_transporte_id')->nullable()->constrained('unidad_transportes')->onDelete('set null'); // Optional
            $table->date('fecha_gasto');
            $table->string('tipo_gasto', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('monto_bs', 10, 2);
            $table->boolean('comprobante_adjunto')->default(false);
            $table->enum('estado_aprobacion', ['Pendiente', 'Aprobado', 'Rechazado'])->default('Pendiente');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null'); // Optional
            $table->timestamps(); // Coincide con fecha_creacion y fecha_actualizacion
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gasto_extra_transportes');
    }
};