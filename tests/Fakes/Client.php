<?php

namespace AhmadWaleed\Soquel\Tests\Fakes;

use AhmadWaleed\Soquel\Query\QueryableInterface;

class Client implements QueryableInterface
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
}
