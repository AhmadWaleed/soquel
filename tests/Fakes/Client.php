<?php

namespace AhmadWaleed\Soquel\Tests\Fakes;

use Omniphx\Forrest\Client as ForrestClient;

class Client extends ForrestClient
{
    protected array $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    public function query($query, $options = []): array
    {
        return $this->response;
    }

    public function authenticate(): array
    {
        return [];
    }

    public function refresh()
    {
        return true;
    }

    public function revoke()
    {
        return true;
    }
}
