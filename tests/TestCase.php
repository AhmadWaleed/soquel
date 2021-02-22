<?php

namespace AhmadWaleed\Soquel\Tests;

use AhmadWaleed\Soquel\Tests\Fakes\Client;
use AhmadWaleed\Soquel\SoquelServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->config['forrest.storage.type'] = 'cache';
        $this->app->instance('forrest', new Client);
        $this->app->instance('encrypter', new class {
            public function decrypt($value, $unserialize = true)
            {
                return [];
            }
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            SoquelServiceProvider::class,
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
