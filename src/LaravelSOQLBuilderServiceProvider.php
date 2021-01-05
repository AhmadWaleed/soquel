<?php

namespace AhmadWaleed\LaravelSOQLBuilder;

use Illuminate\Support\ServiceProvider;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryableInterface;
use AhmadWaleed\LaravelSOQLBuilder\Commands\MakeObjectCommand;

class LaravelSOQLBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-soql-builder.php' => config_path('laravel-soql-builder.php'),
            ], 'config');

            $this->commands([
                MakeObjectCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-soql-builder.php', 'laravel-soql-builder');

        $this->app->bind(QueryableInterface::class, fn () => new SOQLClient(app('forrest')));
        $this->app->alias(QueryableInterface::class, 'soql-client');
    }
}
