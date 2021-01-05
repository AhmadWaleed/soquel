<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use AhmadWaleed\LaravelSOQLBuilder\Query\QueryBuilder;

abstract class SalesForceObject implements ObjectInterface
{
    abstract public static function create(array $self): self;

    public static function newQuery(): QueryBuilder
    {
        return new QueryBuilder(new static, app('soql-client'));
    }

    public function getNamespace(): string
    {
        return config('laravel-soql-builder.default_namespace');
    }
}
