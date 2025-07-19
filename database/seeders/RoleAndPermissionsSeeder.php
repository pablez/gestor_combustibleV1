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
        Permission::firstOrCreate(['name' => 'editar solicitudes combustible']);
        Permission::firstOrCreate(['name' => 'aprobar solicitudes combustible']);
        Permission::firstOrCreate(['name' => 'rechazar solicitudes combustible']);

        // No hay 'eliminar despachos' por historial

        // Permisos de Consumo de Combustible
        Permission::firstOrCreate(['name' => 'ver consumo combustible']);
        Permission::firstOrCreate(['name' => 'registrar consumo combustible']);
        Permission::firstOrCreate(['name' => 'editar consumo combustible']);

        // Permisos de Gastos Extra Transporte
        Permission::firstOrCreate(['name' => 'ver gastos extra']);
        Permission::firstOrCreate(['name' => 'registrar gastos extra']);
        Permission::firstOrCreate(['name' => 'aprobar gastos extra']);
        Permission::firstOrCreate(['name' => 'rechazar gastos extra']);


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
        
        // Permisos de Unidades Organizacionales
        Permission::firstOrCreate(['name' => 'ver unidades organizacionales']);
        Permission::firstOrCreate(['name' => 'crear unidades organizacionales']);
        Permission::firstOrCreate(['name' => 'editar unidades organizacionales']);

        //Permisos de Presupuesto y Finanzas
        Permission::firstOrCreate(['name' => 'ver presupuesto']);
        Permission::firstOrCreate(['name' => 'crear presupuesto']);
        Permission::firstOrCreate(['name' => 'editar presupuesto']);
        Permission::firstOrCreate(['name' => 'eliminar presupuesto']);

        //Permisos de fuentes de financiamiento
        Permission::firstOrCreate(['name' => 'ver fuentes de financiamiento']);
        Permission::firstOrCreate(['name' => 'crear fuentes de financiamiento']);
        Permission::firstOrCreate(['name' => 'editar fuentes de financiamiento']);
        Permission::firstOrCreate(['name' => 'eliminar fuentes de financiamiento']);

        // Permisos de Categorías Programáticas
        Permission::firstOrCreate(['name' => 'ver categorias programaticas']);
        Permission::firstOrCreate(['name' => 'crear categorias programaticas']);
        Permission::firstOrCreate(['name' => 'editar categorias programaticas']);
        Permission::firstOrCreate(['name' => 'eliminar categorias programaticas']);

                // --- Módulo: Presupuesto y Finanzas (NUEVO) ---
        Permission::firstOrCreate(['name' => 'ver informacion presupuestaria']);

        // Permisos de Auditoría
        Permission::firstOrCreate(['name' => 'ver auditoria']);
        Permission::firstOrCreate(['name' => 'ver reportes']);

        // 2. Crear Roles
        $adminGeneralRole = Role::firstOrCreate(['name' => 'Admin General']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);
        $conductorRole = Role::firstOrCreate(['name' => 'Conductor/Operador']);

        // 3. Asignar todos los permisos al rol de Administrador
        $adminGeneralRole->givePermissionTo(Permission::all());

        $adminRole->givePermissionTo([
            // Permisos de Usuarios
            'crear usuarios',
            'ver usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'aprobar usuarios',
            // Permisos de Roles
            'ver roles',
            'crear roles',
            'editar roles',
            'eliminar roles',
            // Permisos de Unidades de Transporte
            'ver unidades',
            'crear unidades',
            'editar unidades',
            'eliminar unidades',
            // Permisos de Solicitudes de Combustible
            'ver solicitudes combustible',
            'crear solicitudes combustible',
            'aprobar solicitudes combustible',
            'editar solicitudes combustible',
            'rechazar solicitudes combustible',
            // Permisos de Despachos de Combustible
            'ver consumo combustible',
            'registrar consumo combustible',
            'editar consumo combustible',
            // Permisos de Gastos Extra Transporte
            'ver gastos extra',
            'registrar gastos extra',
            'aprobar gastos extra',
            'rechazar gastos extra',
            // Permisos de Proveedores
            'ver proveedores',
            'crear proveedores',
            'editar proveedores',
            'eliminar proveedores',
            // Permisos de Normativas
            'ver normativas', 
            'crear normativas', 
            'editar normativas', 
            'eliminar normativas',
            // Permisos de Unidades Organizacionales
            'ver unidades organizacionales',
            'crear unidades organizacionales',
            'editar unidades organizacionales',

            // Permisos de Presupuesto y Finanzas
            'ver presupuesto', 
            'crear presupuesto', 
            'editar presupuesto', 

            // Permisos de Fuentes de Financiamiento
            'ver fuentes de financiamiento',
            'crear fuentes de financiamiento',
            'editar fuentes de financiamiento',
            'eliminar fuentes de financiamiento',

            // Permisos de Categorías Programáticas
            'ver categorias programaticas',
            'crear categorias programaticas',
            'editar categorias programaticas',
            'eliminar categorias programaticas',

            // Permisos de Información Presupuestaria
            'ver informacion presupuestaria',

            // Permisos de Auditoría
            'ver auditoria', 
            'ver reportes',

        ]);

        // 4. Asignar permisos específicos a Supervisor
        $supervisorRole->givePermissionTo([

            // Permisos de Usuarios
            'crear usuarios',
            'ver usuarios',
            'editar usuarios',
            // Permisos de Roles
            'ver roles',

            // Permisos de Unidades de Transporte
            'ver unidades',
            'crear unidades',
            'editar unidades',
            // Permisos de Solicitudes de Combustible
            'ver solicitudes combustible',
            'crear solicitudes combustible',
            'aprobar solicitudes combustible',
            'editar solicitudes combustible',
            'rechazar solicitudes combustible',
            // Permisos de Despachos de Combustible
            'ver consumo combustible',
            'registrar consumo combustible',
            // Permisos de Gastos Extra Transporte
            'ver gastos extra',
            'registrar gastos extra',
            'aprobar gastos extra',
            'rechazar gastos extra',
            // Permisos de Proveedores
            'ver proveedores',
            'crear proveedores',
            // Permisos de Normativas
            'ver normativas', 
            'crear normativas', 
            'editar normativas', 
            // Permisos de Unidades Organizacionales
            'ver unidades organizacionales',

            // Permisos de Presupuesto y Finanzas
            'ver presupuesto', 
            'crear presupuesto', 
            'editar presupuesto', 

            // Permisos de Fuentes de Financiamiento
            'ver fuentes de financiamiento',
            'crear fuentes de financiamiento',
            'editar fuentes de financiamiento',

            // Permisos de Categorías Programáticas
            'ver categorias programaticas',
            'crear categorias programaticas',
            'editar categorias programaticas',

            // Permisos de Información Presupuestaria
            'ver informacion presupuestaria',
            
            // Permisos de Auditoría
            'ver auditoria', 
            'ver reportes'
        ]);

        // 5. Asignar permisos específicos a Conductor/Operador
        $conductorRole->givePermissionTo([
            'ver unidades',
            'crear solicitudes combustible',
            'ver solicitudes combustible',
            'registrar consumo combustible',
            'ver consumo combustible',
            'registrar gastos extra',
            'ver gastos extra',
            'ver proveedores',
            'ver normativas',
            'ver unidades organizacionales',
            'ver presupuesto',
            'ver fuentes de financiamiento',
            'ver categorias programaticas',
            'ver informacion presupuestaria',
            'ver auditoria',
            'ver reportes'
        ]);

        // ====== CREAR USUARIOS DE EJEMPLO ======
        
        $adminGeneralUser = User::firstOrCreate(
            ['email' => 'admingeneral@example.com'],
            [
                'nombre' => 'Admin',
                'apellido' => 'General',
                'password' => bcrypt('password'),
                'estado' => 'Activo',
            ]
        );
        $adminGeneralUser->assignRole('Admin General');

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'password' => bcrypt('password'),
                'estado' => 'Activo',
            ]
        );
        $adminUser->assignRole('Admin');

        $supervisorUser = User::firstOrCreate(
            ['email' => 'supervisor@example.com'],
            [
                'nombre' => 'Supervisor',
                'apellido' => 'Jefe',
                'password' => bcrypt('password'),
                'estado' => 'Activo',
            ]
        );
        $supervisorUser->assignRole('Supervisor');

        $conductorUser = User::firstOrCreate(
            ['email' => 'conductor@example.com'],
            [
                'nombre' => 'Conductor',
                'apellido' => 'Operador',
                'password' => bcrypt('password'),
                'estado' => 'Activo',
                'supervisor_id' => $supervisorUser->id,
            ]
        );
        $conductorUser->assignRole('Conductor/Operador');

        $this->command->info('✅ Roles y permisos creados/actualizados exitosamente.');
        $this->command->info('📋 Jerarquía implementada: Admin General > Admin > Supervisor > Conductor/Operador');
    }
}
