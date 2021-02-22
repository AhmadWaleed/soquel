<?php

namespace AhmadWaleed\Soquel;

use Illuminate\Support\ServiceProvider;
use AhmadWaleed\Soquel\Commands\MakeObjectCommand;

class SoquelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeObjectCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->app->register(\Omniphx\Forrest\Providers\Laravel\ForrestServiceProvider::class);
    }
}
