<?php

namespace AhmadWaleed\Soquel;

use Illuminate\Support\Facades\Cache;
use AhmadWaleed\Soquel\Query\ClientInterface;

class SOQLClient implements ClientInterface
{
    /** @var \Omniphx\Forrest\Client */
    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function query(string $soql): array
    {
        return $this->client->query($soql)['records'];
    }

    public function authenticate(): array
    {
        config()->set('forrest.storage.type', 'cache');

        if (! Cache::has(config('forrest.storage.path') . 'token')) {
            $this->client->authenticate();
        }

        return decrypt(Cache::get(config('forrest.storage.path') . 'token'));
    }
}
