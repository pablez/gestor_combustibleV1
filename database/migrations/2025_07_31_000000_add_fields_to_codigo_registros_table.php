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
        Schema::table('codigo_registros', function (Blueprint $table) {
            $table->string('rol_solicitado', 32)->after('vigente_hasta');
            $table->unsignedBigInteger('unidad_organizacional_id')->after('rol_solicitado');
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('unidad_organizacional_id');
            $table->unsignedBigInteger('creado_por')->after('supervisor_id');
            $table->boolean('usado')->default(false)->after('creado_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('codigo_registros', function (Blueprint $table) {
            $table->dropColumn([
                'rol_solicitado',
                'unidad_organizacional_id',
                'supervisor_id',
                'creado_por',
                'usado',
            ]);
        });
    }
};
