<?php

namespace AhmadWaleed\Soquel;

use Illuminate\Support\Facades\Cache;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class Soquel
{
    public static function authenticate(): array
    {
        config()->set('forrest.storage.type', 'cache');

        if (! Cache::has(config('forrest.storage.path') . 'token')) {
            Forrest::authenticate();
        }

        return decrypt(Cache::get(config('forrest.storage.path') . 'token'));
    }
}
