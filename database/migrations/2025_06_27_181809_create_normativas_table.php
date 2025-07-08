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
        Schema::create('normativas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_norma', 255);
            $table->text('descripcion')->nullable();
            $table->enum('tipo_norma', ['Combustible', 'Viaticos', 'Software Libre', 'Otros']);
            $table->decimal('valor_limite', 10, 2)->nullable();
            $table->string('unidad_limite', 50)->nullable();
            $table->enum('periodo_limite', ['Diario', 'Semanal', 'Mensual', 'Por Mision', 'N/A'])->nullable();
            $table->date('fecha_vigencia_inicio')->nullable();
            $table->date('fecha_vigencia_fin')->nullable();
            $table->timestamps(); // Aunque no estaba explícitamente, es buena práctica tenerlas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('normativas');
    }
};