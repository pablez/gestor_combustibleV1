<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UnidadOrganizacional;

class UnidadOrganizacionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = [
            [
                'nombre_unidad' => 'Despacho de la Gobernacion',
                'siglas' => 'DG',
                'tipo_unidad' => 'Superior',
                'descripcion' => 'Unidad directiva principal de la organización',
                'fecha_creacion' => now(),
                'activa' => true,
            ],
            [
                'nombre_unidad' => 'Secretaría Departamental de Planificacion y Desarrollo Estrategico',
                'siglas' => 'SDPDE',
                'tipo_unidad' => 'Ejecutiva',
                'descripcion' => 'Unidad encargada de la administración general',
                'fecha_creacion' => now(),
                'activa' => true,
            ],
            [
                'nombre_unidad' => 'Secretaría Departamental de Finanzas y Administración',
                'siglas' => 'SDFA',
                'tipo_unidad' => 'Ejecutiva',
                'descripcion' => 'Unidad encargada de la gestión financiera y administrativa',
                'fecha_creacion' => now(),
                'activa' => true,
            ],
            [
                'nombre_unidad' => 'Secretaría Departamental de Desarrollo Humano, Culturas y Turismo',
                'siglas' => 'SDHCT',
                'tipo_unidad' => 'Ejecutiva',
                'descripcion' => 'Unidad encargada de la promoción y desarrollo humano, cultural y turístico',
                'fecha_creacion' => now(),
                'activa' => true,
            ],
            [
                'nombre_unidad' => 'Secretaría Departamental de Salud',
                'siglas' => 'SDS',
                'tipo_unidad' => 'Ejecutiva',
                'descripcion' => 'Unidad encargada de la gestión de la salud pública',
                'fecha_creacion' => now(),
                'activa' => true,
            ],
        ];

        foreach ($unidades as $unidad) {
            UnidadOrganizacional::firstOrCreate(
                ['siglas' => $unidad['siglas']],
                $unidad
            );
        }

        $this->command->info('✅ Unidades organizacionales con siglas creadas exitosamente.');
    }
}
