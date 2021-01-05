<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests\Fakes;

use AhmadWaleed\LaravelSOQLBuilder\Query\QueryableInterface;

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
