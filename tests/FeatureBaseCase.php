<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class FeatureBaseCase extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var String
     */
    protected $loginToken;

    protected $headers = [];

    public function setUp(): void
    {
        parent::setUp();
    }
}
