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
        Schema::table('users', function (Blueprint $table) {
            // Añadir la columna para el ID del supervisor.
            // Es una clave foránea que apunta a la misma tabla de usuarios.
            // Se permite que sea nulo, ya que no todos los usuarios tendrán un supervisor.
            // Si un supervisor es eliminado, el campo supervisor_id de los usuarios asignados se establecerá en null.
            $table->foreignId('supervisor_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la restricción de clave foránea antes de eliminar la columna.
            // El nombre de la restricción sigue la convención de Laravel: table_column_foreign
            $table->dropForeign(['supervisor_id']);
            // Eliminar la columna supervisor_id.
            $table->dropColumn('supervisor_id');
        });
    }
};
