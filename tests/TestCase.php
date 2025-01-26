<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use InovantiBank\Holidays\Providers\HolidaysServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            HolidaysServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
