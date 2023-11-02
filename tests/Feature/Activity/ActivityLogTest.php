<?php

namespace Tests\Feature\Activity;

use App\Models\User;
use Tests\FeatureBaseCase;

class ActivityLogTest extends FeatureBaseCase
{
    /**
     * A basic feature test example.
     */
    public function testUserCanSeeActivityLogList(): void
    {

        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->givePermissionTo('read_logs');


        $response = $this->actingAs($user)->getJson(route('admin.logs.index'));


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'log_name',
                    'description',
                    'target',
                    'activity',
                    'ip',
                    'created_at',
                ],
            ],
            "meta" => [
                'current_page',
                'from',
                'links',
                'per_page',
                'to',
                'total',
            ]
        ]);
    }


    public function testUserCanNotSeeActivityLogList(): void
    {
        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $response = $this->actingAs($user)->getJson(route('admin.logs.index'));


        $response->assertStatus(403);
    }


    public function testUserCanNotDownloadActivityLogList(): void
    {
        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user = User::factory()->create();

        $user->revokePermissionTo('download_logs');


        $response = $this->actingAs($user)->getJson(route('admin.logs.download'));


        $response->assertStatus(403);
    }



    public function testUserCanDownloadActivityLogList(): void
    {

        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->givePermissionTo('download_logs');


        $response = $this->actingAs($user)->getJson(route('admin.logs.download'));


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'log_name',
                    'description',
                    'target',
                    'activity',
                    'ip',
                    'created_at',
                ],
            ]
        ]);
    }
}
