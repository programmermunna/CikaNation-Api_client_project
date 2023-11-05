<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        activity('User Login')->causedBy($user->id)
            ->performedOn($user)
            ->withProperties([
                'ip' => '127.0.0.1',
                'target' => $user->username,
                'activity' => 'Dummy test activity',
            ])
            ->log('Dummy test activity');
    }
}
