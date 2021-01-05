<?php

namespace AhmadWaleed\LaravelSOQLBuilder;

use Omniphx\Forrest\Client;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryableInterface;

class SOQLClient implements QueryableInterface
{
    protected Client $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function query(string $soql): array
    {
        return $this->client->query($soql)['records'];
    }
}
