<?php

namespace AhmadWaleed\Soquel;

use Omniphx\Forrest\Client;
use AhmadWaleed\Soquel\Query\QueryableInterface;

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
