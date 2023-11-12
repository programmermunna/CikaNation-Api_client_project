<?php

namespace Tests\Feature\Api;

use App\Models\Cashflow;
use App\Models\User;
use Database\Factories\CashflowFactory;
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
                    'created_by' => [],
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
        $response->assertJsonValidationErrorFor('item_price', 'errors');
    }


    public function testImageUploadValidationError()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $image = UploadedFile::fake()->image('banner.png', 200, 200);

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
        $image = UploadedFile::fake()->image('banner.pdf', 200, 200); // pdf file type


        $response = $this->actingAs($user)->postJson(route('service.cashflows.store'), [
            'image' => $image,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('image', 'errors');
    }



    public function testCashflowCreationSuccessfully()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $image = UploadedFile::fake()->image('banner.png', 200, 200);

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
                'created_at',
                'created_at' => [],
            ]
        ]);
    }



    public function testItemNameRequiredValidationOnUpdate()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();
        $image = UploadedFile::fake()->image('banner.png', 200, 200); // pdf file type

        $cashflow = Cashflow::first();

        $response = $this->actingAs($user)->putJson(route('service.cashflows.update', $cashflow->id), [
            'item_name' => '',
            'item_price' => 1200,
            'image' => $image,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('item_name', 'errors');
    }

    public function testItemPriceRequiredValidationOnUpdate()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();
        $image = UploadedFile::fake()->image('banner.png', 200, 200); // pdf file type

        $cashflow = Cashflow::first();

        $response = $this->actingAs($user)->putJson(route('service.cashflows.update', $cashflow->id), [
            'item_name' => 'update name',
            'item_price' => '',
            'image' => $image,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('item_price', 'errors');
    }



    /**
     * Cashflow update test case
     */
    public function testCashflowUpdateSuccessfully()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $image = UploadedFile::fake()->image('banner.png', 200, 200);

        $cashflow = Cashflow::first();

        $response = $this->actingAs($user)->putJson(route('service.cashflows.update', $cashflow->id), [
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
                'created_by' => [],
                'created_at',
            ]
        ]);
    }




    public function testSoftDeleteCashflow()
    {

        $this->artisan('migrate:fresh --seed');

        $cashflow = Cashflow::factory()->create();// create cashflow
        $cashflow->delete();        // delete cashflow
        $cashflow = Cashflow::withTrashed()->find($cashflow->id); // retrive cashflow with trashed

        $this->assertSoftDeleted('cashflows', [
            'id'          => $cashflow->id,
            'item_name'   => $cashflow->item_name,
            'item_price' => $cashflow->item_price,
            'upload' => $cashflow->upload
        ]);
    }



    public function testDeleteSingleCashflow()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('service.cashflows.destroy', Cashflow::first()->id));
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Cashflow deleted successfully'
        ]);
    }



    public function testDeleteManyCashflow()
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();


        $cashflowId = Cashflow::take(5)->pluck('id')->toArray();

        $response = $this->actingAs($user)->deleteJson(route('service.cashflows.delete_many'), [
            'cashflow_id' => $cashflowId
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Cashflows are deleted successfully'
        ]);
    }


    /**
     * @test
     *
     * @dataProvider cashflowData
     */
    public function testCashflowInputValidation($credentials, $errors, $errorKeys)
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->postJson(route('service.cashflows.store'), $credentials);

        $response->assertJsonValidationErrors($errorKeys);

        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $response->assertStatus(422);
    }



    /**
     * @test
     *
     * @dataProvider cashflowId
     */
    public function testCashflowDeleteMultipleInputValidation($credentials, $errors, $errorKeys)
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::where('username', 'administrator')->first();

        $response = $this->actingAs($user)->deleteJson(route('service.cashflows.delete_many'), $credentials);

        $response->assertJsonValidationErrors($errorKeys);

        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $response->assertStatus(422);
    }



    public static function cashflowId(): array
    {
        return [
            [
                [
                    'cashflow_id',
                ],
                [
                    'cashflow_id' => [
                        "The cashflow id field is required."
                    ]
                ],
                [
                    'cashflow_id'
                ]
            ],
            [
                [
                    'cashflow_id' => 1
                ],
                [
                    "cashflow_id" => [
                        "The cashflow id field must be an array."
                    ]
                ],
                [
                    'cashflow_id',
                ]
            ],

            [
                [
                    'cashflow_id' => [
                        1, 2, "abc", "not exists", 89898989, 23423
                    ]
                ],
                [
                    "cashflow_id" => [
                        "The selected cashflow id is invalid."
                    ]
                ],
                [
                    'cashflow_id',
                ]
            ],

        ];
    }



    public static function cashflowData(): array
    {

        return [
            [
                [
                    'item_name' => 'item name',
                    'item_price' => 1200,
                ],
                [
                    'image' => [
                        'The image field is required.',
                    ],
                ],
                [
                    'image'
                ]

            ],
            [
                [
                    'item_name' => 'Item name',
                    'image'     => UploadedFile::fake()->image('banner.pdf', 200, 200)

                ],
                [
                    'item_price' => [
                        'The item price field is required.'
                    ],
                ],
                [
                    'item_price'
                ]
            ],
            [
                [

                    'item_price' => 1200,
                    'image'      => UploadedFile::fake()->image('banner.pdf', 200, 200)
                ],
                [
                    'item_name' => [
                        'The item name field is required.'
                    ],
                ],
                [
                    'item_name'
                ]
            ]
        ];
    }
}
