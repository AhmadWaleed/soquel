<?php

namespace AhmadWaleed\Soquel\Query;

interface ClientInterface
{
    public function query(string $soql): array;
}
