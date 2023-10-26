<?php

namespace Tests\Feature\Role;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_role_creation(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
