<?php

namespace Tests\Feature\Ip;

use App\Models\User;
use App\Models\UserIp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\FeatureBaseCase;

class UserIpTest extends FeatureBaseCase
{
    use RefreshDatabase;

    /**
     * User Ip List
     */
    public function testUserIpList(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

            UserIp::create([
                'ip_address' => '103.15.245.75',
                'description' => 'testing Ip',
                'whitelisted' => 1,
                'created_by' => 2,
            ]);

        $user->assignRole(Role::where('name', 'Administrator')->first());


        $response = $this->actingAs($user)->getJson('/api/v1/user-ip');


        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip1',
                    'ip2',
                    'ip3',
                    'ip4',
                    'ip_address',
                    'whitelisted',
                    'description',
                    'created_at',
                ]
            ],
            'links',
            'meta'
        ]);


    }

    /**
     * User Ip Creation
     */
    public function testUserWithAppropriatePrivilegeCanIpCreate(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        $response = $this->actingAs($user)
            ->postJson('/api/v1/user-ip', [
            'number1' => 103,
            'number2' => 15,
            'number3' => 245,
            'number4' => 75,
            'description' => 'testing description',
        ]);


        $response->assertStatus(200);

        $this->assertDatabaseHas('user_ips', [
            'ip_address' => '103.15.245.75',
            'whitelisted' => 1,
        ]);

        $response->assertJsonStructure([
            "status",
            "message",
            "data" => [
                "ip_address",
                "description",
                "created_by",
                "created_at",
                "id",
            ]
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => true
        ]);
    }

   /**
    * User Ip Update
    */
   public function testUserIpUpdate()
   {
       $this->artisan('migrate:fresh --seed');

       $user = User::factory()
           ->state([
               'active' => true
           ])
           ->createQuietly();


       $user->assignRole(Role::where('name', 'Administrator')->first());


       $userIp = UserIp::factory()->create();


       $response = $this->actingAs($user)->putJson('/api/v1/user-ip/'.$userIp->id.'', [
           'number1' => 103,
           'number2' => 15,
           'number3' => 245,
           'number4' => 75,
           'whitelisted' => 1,
           'description' => 'testing updated descriptoin',
       ]);

       $response->assertStatus(200);
       $response->assertJsonStructure([
           "status",
           "message",
           "data" => [
               "id",
               "ip_address",
               "description",
               "whitelisted",
               "created_by",
               "updated_by",
               "deleted_by",
               "deleted_at",
               "created_at",
               "updated_at",
           ]
       ]);

       $response->assertJson([
           'status' => true,
           'message' => true,
           'data' => true
       ]);
   }

   /**
    * Users Ip Update Multiple
    */
   public function testUserIpUpdateMultiple()
   {
       $this->artisan('migrate:fresh --seed');

       $user = User::factory()
           ->state([
               'active' => true
           ])
           ->createQuietly();

           $user->assignRole(Role::where('name', 'Administrator')->first());

           UserIp::factory(2)->create();

           $data = [
               "items" => [
                   [
                       "id" => 1,
                       "item" => [
                           "number1" => "103",
                           "number2" => "15",
                           "number3" => "245",
                           "number4" => "80",
                           "whitelisted" => 1,
                           "description" => "testing Ip Updated"
                       ]
                   ],
                   [
                       "id" => 2,
                       "item" => [
                           "number1" => "103",
                           "number2" => "15",
                           "number3" => "245",
                           "number4" => "90",
                           "whitelisted" => 1,
                           "description" => "testing Ip Updated"
                       ]
                   ]
               ]
           ];


       $response = $this->actingAs($user)->putJson('/api/v1/user-ips',$data);

       $response->assertStatus(200);
       $response->assertJsonStructure([
           "status",
           "message",
           "data"
       ]);

       $response->assertJson([
           'status' => true,
           'message' => true,
           'data' => true
       ]);
   }



    /**
     * User Ip Delete single or multiple
     */
    public function test_userIpDelete(): void
    {
        $this->artisan('migrate:fresh --seed');

        $user = User::factory()
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $user->assignRole(Role::where('name', 'Administrator')->first());

        UserIp::create([
            'ip_address' => '103.15.245.74',
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by' => 1,
            'created_at' => now(),
        ]);

        UserIp::create([
            'ip_address' => '103.15.245.75',
            'whitelisted' => 1,
            'description' => 'testing ip update',
            'created_by' => 1,
            'created_at' => now(),
        ]);


        $response = $this->actingAs($user)->DeleteJson('/api/v1/user-ip/1,2');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "data",
        ]);

        $response->assertJson([
            'status' => true,
            'message' => true,
            'data' => false
        ]);
    }
}
