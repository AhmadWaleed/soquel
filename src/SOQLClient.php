<?php

namespace AhmadWaleed\Soquel;

use AhmadWaleed\Soquel\Query\QueryableInterface;

class SOQLClient implements QueryableInterface
{
    public function query(string $soql): array
    {
        return app('forrest')->query($soql)['records'];
    }
}
