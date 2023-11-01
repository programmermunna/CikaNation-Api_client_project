<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::first();

        if (!$role) {
            $role = Role::create(['name' => 'Administrator']);
        }

        $role->syncPermissions(Permission::get()->pluck('id')->toArray());
    }
}
