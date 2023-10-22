<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureBaseCase;

class UserLoginTest extends FeatureBaseCase
{
    use RefreshDatabase;
    public function testUserCanSuccessfullyLogin()
    {
        $user = User::factory()
            ->createQuietly();

        $response = $this->post('/api/v1/login', [
            'username' => $user->username,
            'password' => 'password',
        ], $this->headers);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'username',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'created_by',
                    'updated_by',
                    'deleted_by',
                ],
            ]
        ]);

        $response->assertJson([
            'status' => 'success',
            'token_type' => 'Bearer',
            'data' => [
                'token_type' => 'Bearer',
                'token_type3' => 'Bearer',
            ]
        ]);
    }
}
