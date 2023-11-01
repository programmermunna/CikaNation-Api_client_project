<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preventDualInsert = [
            'user.access.user.ip.create' => Permission::where('name', 'user.access.user.ip.create')->first(),
            'user.access.user.ip.edit' => Permission::where('name', 'user.access.user.ip.edit')->first(),
            'user.access.user.ip.view' => Permission::where('name', 'user.access.user.ip.view')->first(),
            'user.access.user.ip.delete' => Permission::where('name', 'user.access.user.ip.delete')->first(),
        ];


        foreach ($preventDualInsert as $permissionName => $permission) {
            $verb = explode('.', $permissionName);
            $verb = end($verb);
            if (! $permission) {
                $permission = Permission::create([
                    'type' => 'user',
                    'guard_name' => 'api',
                    'name' => $permissionName,
                    'description' => "Can $verb User Ip",
                    'parent_id' => null,
                    'sort' => 8,
                    'group_by' => 'user',
                    'modul_name' => 'user',
                    'created_at' => now(),
                ]);

                $role = Role::firstOrCreate([
                    'name' => 'Administrator'
                ], [
                    'name' => 'Administrator'
                ]);

                $role->permissions()->attach([
                    $permission->id
                ]);
            }
        }
    }
}
