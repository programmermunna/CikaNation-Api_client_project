<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\FeatureBaseCase;

class CashflowTest extends FeatureBaseCase
{
    /**
     * A basic feature test example.
     */
    public function testCashflowList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->getJson(route('service.cashflows.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'item_name',
                    'upload',
                    'created_at',
                ]
            ],
            'links',
            'meta',
        ]);
    }


    public function testCashflowStoreValidationError()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->postJson(route('service.cashflows.store'), [
            'item_name' => '',
            'item_price' => '',
            'image' => '',
        ]);

        $response->assertStatus(422);

        $response->assertJson([
            "message" => "The item name field is required. (and 2 more errors)",
            'errors' => [
                'item_name' => [
                    "The item name field is required."
                ],
                'item_price' => [
                    "The item price field is required."
                ],
                'image' => [
                    "The image field is required."
                ]
            ]
        ]);
    }


    public function testItemPriceNumericDataValidationError()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->postJson(route('service.cashflows.store'), [
            'item_price' => 'non numeric value',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('item_price','errors');
    }


    public function testImageUploadValidationError()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $image = UploadedFile::fake()->image('banner.png',200,200);

        $response = $this->actingAs($user)->postJson(route('service.cashflows.store'), [
            'image'      => $image,
        ]);
        $response->assertStatus(422);
        $response->assertJsonMissingValidationErrors('image',); 
    }



    public function testInvalidImageExtensionValidationError()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();
        $image = UploadedFile::fake()->image('banner.pdf',200,200); // pdf file type


        $response = $this->actingAs($user)->postJson(route('service.cashflows.store'), [
            'image' => $image,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('image','errors');
    }



    public function testCashflowCreation()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $image = UploadedFile::fake()->image('banner.png',200,200);

        $response = $this->actingAs($user)->postJson(route('service.cashflows.store'), [
            'item_name'  => 'Item name',
            'item_price' => 1200,
            'image'      => $image,
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'item_name',
                'item_price',
                'upload',
                'created_by',
                'created_at',
            ]
        ]);
    }
}
