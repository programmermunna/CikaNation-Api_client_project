<?php

namespace Tests\Feature\Role;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Testing\Fluent\AssertableJson;


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
        
        $user->givePermissionTo('create_roles');


        $response = $this->actingAs($user)->postJson(route('admin.roles.store'), [
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

    public function testUserRoleUpdate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

            $user->givePermissionTo('update_roles');



        $role = Role::create([
            'name' => 'Test_Role'
        ]);


        $response = $this->actingAs($user)->putJson(route('admin.roles.update', $role->id), [
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

    public function testUserRoleDelete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

            $user->givePermissionTo('delete_roles');



        $role = Role::create([
            'name' => 'Test_Role'
        ]);


        $response = $this->actingAs($user)->deleteJson(route('admin.roles.destroy', $role->id));


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

    public function testUserRoleList(): void
    {
        $this->artisan('migrate:fresh --seed');


        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();
        
        $user->givePermissionTo('read_roles');

        $role = Role::create(['name' => 'Admin']);

        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->getJson(route('admin.roles.index'));


        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('status')
                ->has('data')
                ->has(
                    'data.0',
                    fn (AssertableJson $json) =>
                    $json->has('id')
                        ->has('name')
                        ->has('created_at')
                        ->has('updated_at')
                        ->etc()
                );
        });


    }
}
