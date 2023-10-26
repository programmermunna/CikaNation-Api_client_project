<?php

namespace Tests\Feature\Role;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RoleFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_role_creation(): void
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


    /**
     * Update Role
     */

    public function test_user_role_update(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $role = Role::create([
            'name' => 'Test_Role'
        ]);


        $response = $this->actingAs($user)->putJson(route('roles.update', $role->id), [
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





    /**
     * Delete Role
     */

    public function test_user_role_delete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $role = Role::create([
            'name' => 'Test_Role'
        ]);


        $response = $this->actingAs($user)->deleteJson(route('roles.destroy', $role->id));


        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data"
        ]);
    }

    /**
     * Role List
     */

    public function test_user_role_list(): void
    {
        $this->artisan('migrate:fresh --seed');


        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $response = $this->actingAs($user)->getJson(route('roles.index'));


        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "data"
        ]);
    }
}