<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\RoleAndPermissionsSeeder;
use Database\Seeders\UnidadOrganizacionalSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UnidadOrganizacionalSeeder::class,
            RoleAndPermissionsSeeder::class,
            // ... otros seeders
        ]);
    }
}
