<?php

namespace Tests\Feature\Announcement;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    /**
     * Annuncement Create test example
     */
    public function testAnnouncementCreation(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();


        $response = $this->actingAs($user)->postJson(route('announcements.store'), [
            'message' => 'Dummy text for announcement message' ,
            'status' => rand(0,1)
        ]);


        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "message",
                "number",
                "updated_at",
                "created_at",
                "created_by",
                "id",
            ]
        ]);
    }
}
