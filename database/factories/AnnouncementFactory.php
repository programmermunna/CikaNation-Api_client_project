<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number'     => random_int(1,999999),
            'message'    => $this->faker->sentence(6),
            'status'     => $this->faker->boolean(),
            'created_at' => Carbon::now(),
        ];
    }
}
