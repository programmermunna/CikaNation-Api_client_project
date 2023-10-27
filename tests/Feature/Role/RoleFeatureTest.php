<?php

namespace Tests\Feature\Role;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Support\Str;

class RoleFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testUserRoleCreation(): void
    {
        $this->artisan('migrate:fresh --seed');
              
        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $response = $this->actingAs($user)->postJson(route('roles.store'), [
            'name' => Str::random(10),
            'permissions' => [1, 2, 3]
        ]);


        $response->assertStatus(200);
            $response->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "guard_name",
                    "name",
                    "updated_at",
                    "created_at",
                    "id",
                ]
            ]);
    }




}