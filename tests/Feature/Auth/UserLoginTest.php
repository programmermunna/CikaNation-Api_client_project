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
            ->state([
                'active' => true
            ])
            ->createQuietly();

        $response = $this->postJson(route('login'), [
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
            'data' => [
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * @test
     *
     * @dataProvider userLoginData
     */
    public function testUserLoginInputValidation($credentials, $errors, $errorKeys)
    {
        $user = User::factory()
            ->createQuietly();

        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $response = $this->postJson('/api/v1/login', $credentials, $this->headers);

        $response->assertJsonValidationErrors($errorKeys);
        foreach ($errorKeys as $errorKey) {
            $response->assertJsonValidationErrorFor($errorKey);
        }

        $inValidPasswordResponse = $this->postJson('/api/v1/login', [
            'username' => $user->username,
            'password' => 'someNotCorrectPassword'
        ], $this->headers);

        $inValidPasswordResponse->assertJson([
            "status" => "error",
            "message" => "Invalid Login Credentials"
        ]);
    }

    /**
     * @test
     *
     *
     */
    public function testDeactivatedUserCannotLogin()
    {
        $user = User::factory()
            ->sequence([
                'active' => false
            ])
            ->createQuietly();

        $response = $this->postJson('/api/v1/login', [
            'username' => $user->username,
            'password' => 'password',
        ], $this->headers);

        $response->assertStatus(400);

        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        $response->assertJson([
            'status' => 'error',
            'message' => "Username has been deactivate!."
        ]);
    }
    public static function userLoginData(): array
    {
        return [
            [
                [
                    'username' => 'username',
                    'password' => 'password',
                ],
                [
                    'username' => [
                        'The selected username is invalid.',
                    ],
                ],
                [
                    'username'
                ]
            ],
            [
                [
                    'password' => 'password',
                ],
                [
                    'username' => [
                        'The username field is required.'
                    ],
                ],
                [
                    'username'
                ]
            ],
            [
                [
                    'username' => 'username',
                ],
                [
                    'password' => ['The password field is required.'],
                ],
                [
                    'password'
                ]
            ]
        ];
    }
}
