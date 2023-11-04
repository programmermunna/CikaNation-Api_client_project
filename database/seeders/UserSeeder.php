<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::create([
            'name'     => 'Administrator',
            'username' => 'administrator',
            'email'    => 'test@example.com',
            'password' => Hash::make(12345678),
            'email_verified_at' => now(),
        ]);
        $user->permissions()->sync(Permission::pluck('id')->toArray());

        User::factory(10)->create();
        
    }
}