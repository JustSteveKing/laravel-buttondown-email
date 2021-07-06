<?php

declare(strict_types=1);

namespace JustSteveKing\Laravel\ButtonDownEmail\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use JustSteveKing\Laravel\ButtonDownEmail\ButtonDownServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set(
            'buttondown.api.key',
            'ec47ffe5-3fe5-41fd-9b75-c1007ba2fabe',
        );
        $this->app['config']->set(
            'buttondown.api.url',
            'https://api.buttondown.email/v1',
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ButtonDownServiceProvider::class,
        ];
    }
}
