<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Query;

interface QueryableInterface
{
    public function query(string $soql): array;
}
