<?php

namespace Tests;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Hash::setRounds(4);
        config(['cache.default' => 'array', 'session.driver' => 'array']);
        $this->withoutVite();
        $this->seed(RoleAndPermissionSeeder::class);
    }
}
