<?php

namespace Tests\Feature\Ip;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\FeatureBaseCase;

class UserIpTest extends FeatureBaseCase
{
    use RefreshDatabase;

    /**
     * User Ip Creation
     */
    public function testUserWithAppropriatePrivilegeCanIpCreate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        $response = $this->actingAs($user)
            ->postJson('/api/v1/user-ip', [
            'number1' => 103,
            'number2' => 15,
            'number3' => 245,
            'number4' => 75,
            'description' => 'testing description',
        ]);


        $response->assertStatus(200);

        $this->assertDatabaseHas('user_ips', [
            'ip_address' => '103.15.245.75',
            'whitelisted' => 1,
        ]);

        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "ip_address",
                "whitelisted",
                "description",
                "created_by",
                "created_at",
                "id",
            ]
        ]);

        $response->assertJson([
            'status' => "successful",
            'data' => [
                "ip_address" => "103.15.245.75"
            ]
        ]);
    }

    public function testUserWithoutPermissionCannotCreateIp()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/user-ip', [
                'number1' => 103,
                'number2' => 15,
                'number3' => 245,
                'number4' => 75,
                'description' => 'testing description',
            ]);

        $response->assertStatus(403);
    }
}
