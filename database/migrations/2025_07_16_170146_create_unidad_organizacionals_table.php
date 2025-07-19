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
        Schema::create('unidad_organizacionals', function (Blueprint $table) {
            $table->id('id_unidad_organizacional');
            $table->string('nombre_unidad', 100)->unique();
            $table->string('siglas', 10)->unique();
            $table->enum('tipo_unidad', ['Superior', 'Ejecutiva']);
            $table->text('descripcion')->nullable();
            $table->datetime('fecha_creacion');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_organizacionals');
    }
};
