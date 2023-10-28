<?php

namespace Tests\Feature\Ip;

use App\Models\User;
use App\Models\UserIp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserIpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User Ip List
     */
    public function testUserIpList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

            UserIp::create([
                'ip_address' => '103.15.245.75',
                'description' => 'munna Ip',
                'whitelisted' => 1,
                'created_by' => 2,
            ]);


        $response = $this->actingAs($user)->getJson('/api/v1/user-ip');


        $response->assertStatus(200);

        $response->assertJson([
            'status' => true,
            'data' => true
        ]);


    }

    /**
     * User Ip Creation
     */
    public function test_userIpCreate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $response = $this->actingAs($user)->postJson('/api/v1/user-ip', [
            'number1' => 103,
            'number2' => 15,
            'number3' => 245,
            'number4' => 75,
            'description' => 'testing descriptoin',
        ]);


        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "ip_address",
                "description",
                "created_by",
                "created_at",
                "id",
            ]
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
        ]);
    }
}
