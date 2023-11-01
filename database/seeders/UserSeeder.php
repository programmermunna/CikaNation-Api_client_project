<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $user = \App\Models\User::create([
            'name'     => 'Test User',
            'username' => 'test',
            'email'    => 'test@example.com',
            'password' => Hash::make(12345678),
            'email_verified_at' => now(),
        ]);
        $user->permissions()->sync([1,2,3]);
    }
}