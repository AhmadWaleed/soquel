<?php

namespace AhmadWaleed\Soquel;

use Illuminate\Support\ServiceProvider;
use AhmadWaleed\Soquel\Commands\MakeObjectCommand;

class SoquelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/forrest.php' => config_path('forrest.php'),
            ], 'config');

            $this->commands([
                MakeObjectCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/forrest.php', 'forrest');

        $this->app->register(\Omniphx\Forrest\Providers\Laravel\ForrestServiceProvider::class);
    }
}
