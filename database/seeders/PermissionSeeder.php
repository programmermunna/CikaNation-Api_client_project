<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create([
            'name' => 'view_roles'
        ]);

        Permission::create([
            'name' => 'create_roles'
        ]);

        Permission::create([
            'name' => 'update_roles'
        ]);

        Permission::create([
            'name' => 'destroy_roles'
        ]);
    }
}
