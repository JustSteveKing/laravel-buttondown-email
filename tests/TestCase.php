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
            getenv('BUTTONDOWN_KEY'),
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
