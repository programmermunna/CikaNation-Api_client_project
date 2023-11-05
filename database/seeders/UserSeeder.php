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
        User::factory(10)->create();

        $user = \App\Models\User::create([
            'name'     => 'Administrator',
            'username' => 'administrator',
            'email'    => 'test@example.com',
            'password' => '$2y$10$Okwifu2E/SJ9XlWJEr658ep.acTxxAKj5/9dQgoOTCuCgXygA9AQ.',
            'email_verified_at' => now(),
        ]);
        $user->permissions()->sync(Permission::pluck('id')->toArray());

        User::factory(10)->create();

    }
}
