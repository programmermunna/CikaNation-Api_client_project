<?php

namespace Tests\Feature\Auth;

use App\Models\User;

use Tests\FeatureBaseCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutTest extends FeatureBaseCase
{
    /**
     * A basic feature test example.
     */
    public function testUserCanLogout()
    {
        // Create a user
        $user = User::factory()->create();

        // Generate a token for the user
        $token = JWTAuth::fromUser($user);

        // Add the token to the Authorization header
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Send a POST request to the logout route
        $response = $this->postJson(route('logout'), [], $headers);

        // Check if the user is logged out
        $response->assertStatus(200);


        $response->assertJsonStructure([
            'status',
            'message',
        ]);
        $this->assertGuest(); // check that your user not auth more

    }
}
