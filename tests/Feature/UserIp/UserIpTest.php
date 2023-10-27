<?php

namespace Tests\Feature\UserIp;

use App\Models\User;
use App\Models\UserIps;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserIpTest extends TestCase
{
    /**
     * @test
     */

    public function testUserIpCreation(): void
    {
        $this->artisan('migrate:fresh --seed');

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $userIp = UserIps::first();

        $response = $this->postJson('/api/v1/ip', [
            'number1' => $userIp->ip1,
            'number2' => $userIp->ip2,
            'number3' => $userIp->ip2,
            'number4' => $userIp->ip2,
            'description' => $userIp->description,
        ], $headers);


        $response->assertStatus(200);
        // $response->assertJsonStructure([
        //     "status",
        //     "message",
        //     "data" => [
        //         "guard_name",
        //         "name",
        //         "updated_at",
        //         "created_at",
        //         "id",
        //     ]
        // ]);
    }


}
