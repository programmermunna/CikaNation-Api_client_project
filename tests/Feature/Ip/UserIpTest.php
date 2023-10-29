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
                'description' => 'testing Ip',
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

    /**
     * User Ip Update
     */
    public function test_userIpUpdate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


           $userIp = UserIp::create([
                'ip_address' => '103.15.245.74',
                'whitelisted' => 1,
                'description' => 'testing ip update',
                'created_by' => 1,
                'created_at' => now(),
            ]);


        $response = $this->actingAs($user)->putJson('/api/v1/user-ip/'.$userIp->id.'', [
            'number1' => 103,
            'number2' => 15,
            'number3' => 245,
            'number4' => 75,
            'description' => 'testing updated descriptoin',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "id",
                "ip_address",
                "description",
                "whitelisted",
                "created_by",
                "updated_by",
                "deleted_by",
                "deleted_at",
                "created_at",
                "updated_at",
            ]
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
        ]);
    }
    /**
     * User Ip Delete
     */
    public function test_userIpDelete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


           $userIp = UserIp::create([
                'ip_address' => '103.15.245.74',
                'whitelisted' => 1,
                'description' => 'testing ip update',
                'created_by' => 1,
                'created_at' => now(),
            ]);


        $response = $this->actingAs($user)->DeleteJson('/api/v1/user-ip/'.$userIp->id.'');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data",
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => false
        ]);
    }
}
