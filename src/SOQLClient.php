<?php

namespace AhmadWaleed\Soquel;

use Illuminate\Support\Facades\Cache;
use AhmadWaleed\Soquel\Query\QueryableInterface;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class SOQLClient implements QueryableInterface
{
    public function query(string $soql): array
    {
        return app('forrest')->query($soql)['records'];
    }

    public static function authenticate(): array
    {
        config()->set('forrest.storage.type', 'cache');

        if (! Cache::has(config('forrest.storage.path') . 'token')) {
            Forrest::authenticate();
        }

        return decrypt(Cache::get(config('forrest.storage.path') . 'token'));
    }
}
