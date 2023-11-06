<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserIp>
 */
class UserIpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_address' => '103.15.245.90',
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by' => 1,
            'created_at' => now(),
        ];
    }
}
