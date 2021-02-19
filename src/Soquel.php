<?php

namespace AhmadWaleed\Soquel;

use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class Soquel
{
    public static function authenticate(): array
    {
        config()->set('soquel.forrest.authentication', 'UserPassword');

        $storage = ucwords(config('soquel.forrest.storage.type'));
        if (! $storage::has(config('soquel.forrest.storage.path') . 'token')) {
            Forrest::authenticate();
        }

        return decrypt($storage::get(config('soquel.forrest.storage.path') . 'token'));
    }
}
