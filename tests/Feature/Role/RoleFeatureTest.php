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



    public function tesUserRoleList(): void
    {
        $this->artisan('migrate:fresh --seed');


        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->sync([1, 2, 3]);


        $response = $this->actingAs($user)->getJson(route('roles.index'));


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
