<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

abstract class SalesForceObject implements ObjectInterface
{
    abstract public static function createFromArray(array $object): self;

    public static function newQuery(): QueryBuilder
    {
        return new QueryBuilder(new static);
    }
}
