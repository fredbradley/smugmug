<?php

namespace FredBradley\SmugMug\Tests;

use FredBradley\SmugMug\SmugMugServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Saloon\Http\Faking\MockClient;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [SmugMugServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('k', 32)));
        $app['config']->set('cache.default', 'array');
        $app['config']->set('smugmug.api_key', 'test-api-key');
        $app['config']->set('smugmug.api_secret', 'test-api-secret');
    }

    protected function tearDown(): void
    {
        MockClient::destroyGlobal();

        parent::tearDown();
    }
}
