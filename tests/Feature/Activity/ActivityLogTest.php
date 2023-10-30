<?php

namespace Tests\Feature\Activity;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\Activitylog\Models\Activity;
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


        $response = $this->actingAs($user)->getJson(route('logs.index'));


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data'=>[
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



    public function testUserCanDownloadActivityLogList(): void
    {

        $this->artisan("migrate:fresh --seed");

        $this->artisan("db:seed --class=ActivityLogSeeder");

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $response = $this->actingAs($user)->getJson(route('logs.download'));


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data'=>[
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
