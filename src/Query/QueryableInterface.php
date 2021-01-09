<?php

namespace AhmadWaleed\Soquel\Query;

interface QueryableInterface
{
    public function query(string $soql): array;
}
