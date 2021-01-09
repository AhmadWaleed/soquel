<?php

namespace AhmadWaleed\Soquel;

use Illuminate\Support\ServiceProvider;
use AhmadWaleed\Soquel\Query\QueryableInterface;
use AhmadWaleed\Soquel\Commands\MakeObjectCommand;

class SoquelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/soquel.php' => config_path('soquel.php'),
            ], 'config');

            $this->commands([
                MakeObjectCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/soquel.php', 'soquel');

        $this->app->bind(QueryableInterface::class, fn () => new SOQLClient(app('forrest')));
        $this->app->alias(QueryableInterface::class, 'soql-client');
    }
}
