<?php

namespace Tests\Feature\User;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    /**
     * User Attendance List.
     */
    public function testUserAttendanceList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username','administrator')->first();

        Attendance::create([
            'username' => "test_user1",
            'clock' => "3:33",
            'date' => "06-04-2023",
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/attendance');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'username',
                    'clock',
                    'date',
                    'created_at',
                    'updated_at',
                    'created_by',
                ]
                ],
                'links',
                'meta',
        ]);

        $response->assertJson([
            'data' => [],
            'links' => true,
            'meta' => true,
        ]);

    }
}
