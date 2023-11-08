<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\FeatureBaseCase;
use Tests\TestCase;

class CashflowTest extends FeatureBaseCase
{
    /**
     * A basic feature test example.
     */
    public function testCashflowList(): void
    {
        $this->artisan('migrate:fresh --seed');
        
        $user = User::where('username','administrator')->first();

        $response = $this->actingAs($user)->getJson(route('service.cashflows.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'item_name',
                    'uploads',
                    'created_at',
                ]
                ],
                'links',
                'meta',
        ]);
    }
}
