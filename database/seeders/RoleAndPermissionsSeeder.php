<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos de Usuarios
        Permission::firstOrCreate(['name' => 'crear usuarios']);
        Permission::firstOrCreate(['name' => 'ver usuarios']);
        Permission::firstOrCreate(['name' => 'editar usuarios']);
        Permission::firstOrCreate(['name' => 'eliminar usuarios']);
        Permission::firstOrCreate(['name' => 'aprobar usuarios']);

        // Permisos de Roles
        Permission::firstOrCreate(['name' => 'ver roles']);
        Permission::firstOrCreate(['name' => 'crear roles']);
        Permission::firstOrCreate(['name' => 'editar roles']);
        Permission::firstOrCreate(['name' => 'eliminar roles']);

        // Permisos de Unidades de Transporte
        Permission::firstOrCreate(['name' => 'ver unidades']);
        Permission::firstOrCreate(['name' => 'crear unidades']);
        Permission::firstOrCreate(['name' => 'editar unidades']);
        Permission::firstOrCreate(['name' => 'eliminar unidades']);

        // Permisos de Solicitudes de Combustible
        Permission::firstOrCreate(['name' => 'ver solicitudes combustible']);
        Permission::firstOrCreate(['name' => 'crear solicitudes combustible']);
        Permission::firstOrCreate(['name' => 'aprobar solicitudes combustible']);
        Permission::firstOrCreate(['name' => 'rechazar solicitudes combustible']);
        Permission::firstOrCreate(['name' => 'despachar combustible']);

        // Permisos de Despachos de Combustible
        Permission::firstOrCreate(['name' => 'ver despachos combustible']);
        Permission::firstOrCreate(['name' => 'crear despachos combustible']);
        Permission::firstOrCreate(['name' => 'editar despachos combustible']);
        // No hay 'eliminar despachos' por historial

        // Permisos de Consumo de Combustible
        Permission::firstOrCreate(['name' => 'ver consumo combustible']);
        Permission::firstOrCreate(['name' => 'registrar consumo combustible']);
        Permission::firstOrCreate(['name' => 'editar consumo combustible']);

        // Permisos de Solicitudes de Viáticos
        Permission::firstOrCreate(['name' => 'ver solicitudes viaticos']);
        Permission::firstOrCreate(['name' => 'crear solicitudes viaticos']);
        Permission::firstOrCreate(['name' => 'aprobar solicitudes viaticos']);
        Permission::firstOrCreate(['name' => 'rechazar solicitudes viaticos']);
        Permission::firstOrCreate(['name' => 'liquidar viaticos']);

        // Permisos de Liquidación de Viáticos
        Permission::firstOrCreate(['name' => 'ver liquidaciones viaticos']);
        Permission::firstOrCreate(['name' => 'crear liquidaciones viaticos']);

        // Permisos de Gastos Extra Transporte
        Permission::firstOrCreate(['name' => 'ver gastos extra']);
        Permission::firstOrCreate(['name' => 'registrar gastos extra']);
        Permission::firstOrCreate(['name' => 'aprobar gastos extra']);
        Permission::firstOrCreate(['name' => 'rechazar gastos extra']);

        // Permisos de Mantenimiento
        Permission::firstOrCreate(['name' => 'ver mantenimientos']);
        Permission::firstOrCreate(['name' => 'registrar mantenimiento']);
        Permission::firstOrCreate(['name' => 'editar mantenimiento']);

        // Permisos de Proveedores
        Permission::firstOrCreate(['name' => 'ver proveedores']);
        Permission::firstOrCreate(['name' => 'crear proveedores']);
        Permission::firstOrCreate(['name' => 'editar proveedores']);
        Permission::firstOrCreate(['name' => 'eliminar proveedores']);

        // Permisos de Normativas
        Permission::firstOrCreate(['name' => 'ver normativas']);
        Permission::firstOrCreate(['name' => 'crear normativas']);
        Permission::firstOrCreate(['name' => 'editar normativas']);
        Permission::firstOrCreate(['name' => 'eliminar normativas']);

        // Permisos de Auditoría
        Permission::firstOrCreate(['name' => 'ver auditoria']);

        // 2. Crear Roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);
        $conductorRole = Role::firstOrCreate(['name' => 'Conductor/Operador']);

        // 3. Asignar todos los permisos al rol de Administrador
        $adminRole->givePermissionTo(Permission::all());

        // 4. Asignar permisos específicos a Supervisor
        $supervisorRole->givePermissionTo([
            'crear usuarios','ver usuarios','editar usuarios','eliminar usuarios',
            'ver unidades',
            'ver roles',
            'ver solicitudes combustible', 'aprobar solicitudes combustible', 'rechazar solicitudes combustible',
            'ver despachos combustible',
            'ver consumo combustible',
            'ver solicitudes viaticos', 'aprobar solicitudes viaticos', 'rechazar solicitudes viaticos',
            'liquidar viaticos',
            'ver liquidaciones viaticos',
            'ver gastos extra', 'aprobar gastos extra', 'rechazar gastos extra',
            'ver mantenimientos', 'registrar mantenimiento',
            'ver proveedores',
            'ver normativas',
            'ver auditoria' // Supervisor también puede ver la auditoría
        ]);

        // 5. Asignar permisos específicos a Conductor/Operador
        $conductorRole->givePermissionTo([
            'ver unidades',
            'crear solicitudes combustible',
            'ver solicitudes combustible',
            'ver despachos combustible',
            'registrar consumo combustible',
            'ver consumo combustible',
            'crear solicitudes viaticos',
            'ver solicitudes viaticos',
            'registrar gastos extra',
            'ver gastos extra',
        ]);

        // Opcional: Crear un usuario administrador de ejemplo
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nombre' => 'Admin',
                'apellido' => 'General',
                'password' => bcrypt('password'), 
                'estado' => 'Activo',
            ]
        );
        $user->assignRole('Administrador');

        $user = User::firstOrCreate(
            ['email' => 'supervisor@example.com'],
            [
                'nombre' => 'Supervisor',
                'apellido' => 'Jefe',
                'password' => bcrypt('password'),
                'estado' => 'Activo',
            ]
        );
        $user->assignRole('Supervisor');

        $user = User::firstOrCreate(
            ['email' => 'conductor@example.com'],
            [
                'nombre' => 'Conductor',
                'apellido' => 'Principiante',
                'password' => bcrypt('password'),
                'estado' => 'Activo',
            ]
        );
        $user->assignRole('Conductor/Operador');


        $this->command->info('Roles y permisos creados/actualizados exitosamente.');
    
    }
}
