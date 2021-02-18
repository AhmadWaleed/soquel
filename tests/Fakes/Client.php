<?php

namespace AhmadWaleed\Soquel\Tests\Fakes;

use AhmadWaleed\Soquel\Query\ClientInterface;

class Client implements ClientInterface
{
    protected array $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    public function query(string $soql): array
    {
        return $this->response;
    }

    public function authenticate(): array
    {
        return [];
    }
}
