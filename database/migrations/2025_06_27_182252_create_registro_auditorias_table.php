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
        Schema::create('registro_auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Puede que el usuario se elimine
            $table->dateTime('fecha_hora');
            $table->string('accion_realizada', 255);
            $table->string('entidad_afectada', 100);
            $table->unsignedBigInteger('id_registro_afectado'); // No FK, ya que podría ser de cualquier tabla
            $table->text('detalles_cambio')->nullable();
            $table->timestamps(); // Para created_at y updated_at si se necesita
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_auditorias');
    }
};