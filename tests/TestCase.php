<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use AhmadWaleed\LaravelSOQLBuilder\Tests\Fakes\Client;
use AhmadWaleed\LaravelSOQLBuilder\LaravelSOQLBuilderServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->instance('soql-client', new Client);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelSOQLBuilderServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        /*
        include_once __DIR__.'/../database/migrations/create_skeleton_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}
