<?php

namespace AhmadWaleed\Soquel;

use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class Soquel
{
    public static function authenticate(): array
    {
        $storage = ucwords(config('forrest.storage.type'));
        if (! $storage::has(config('forrest.storage.path') . 'token')) {
            config()->set('forrest.authentication', 'UserPassword');
            Forrest::authenticate();
        }

        return decrypt($storage::get(config('forrest.storage.path') . 'token'));
    }
}
