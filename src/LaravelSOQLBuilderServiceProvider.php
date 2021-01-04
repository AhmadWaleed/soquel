<?php

namespace AhmedWaleed\LaravelSOQLBuilder;

use Illuminate\Support\ServiceProvider;
use AhmedWaleed\LaravelSOQLBuilder\Commands\MakeObjectCommand;

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
    }
}
