<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * User List.
     */
    public function testUserRoleList(): void
    {
        $this->artisan('migrate:fresh --seed');


        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->getJson('/api/v1/user');


        $response->assertStatus(200);

        $response->assertJsonStructure([
            "status",
            "data"
        ]);

        $response->assertJson([
            'status' => true,
            'data' => true
        ]);


    }

    /**
     * User Create.
     */
    public function test_userCreate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

            $role = Role::create(['name' => 'Writer',]);
            $role->permissions()->sync([1,2,3]);


        $response = $this->actingAs($user)->postJson('/api/v1/user', [
            'username' => "test_user",
            'name' => "Test User",
            'email' => "testuser@mail.com",
            'password' => "password",
            'password_confirmation' => 'password',
            'roles' => [1],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "name",
                "username",
                "email",
                "updated_at",
                "created_at",
                "id",
                "roles"
            ]
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
        ]);
    }

    /**
     * User Update.
     */
    public function test_userUpdate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

            $role = Role::create(['name' => 'Writer',]);
            $role->permissions()->sync([1,2,3]);


        $response = $this->actingAs($user)->putJson('/api/v1/user/1', [
            'username' => "test_user",
            'name' => "Test User",
            'email' => "testuser@mail.com",
            'roles' => [1],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "id",
                "type",
                "name",
                "username",
                "email",
                "email_verified_at",
                "active",
                "last_login_ip",
                "timezone",
                "created_at",
                "updated_at",
                "created_by",
                "updated_by",
                "deleted_by",
                "last_login_at",
                "deleted_at",
                "roles"
            ]
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
        ]);
    }


}
