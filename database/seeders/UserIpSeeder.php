<?php

namespace Database\Seeders;

use App\Models\UserIps;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserIpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserIps::create([
            'ip_address' => '103.15.245.74',
            'description' => 'testing munna ip',
            'created_by' => 11,
        ]);
    }
}
