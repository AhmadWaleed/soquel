<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use AhmadWaleed\LaravelSOQLBuilder\Query\Builder;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryableInterface;

abstract class BaseObject implements ObjectInterface
{
    protected Builder $builder;
    protected QueryableInterface $client;
    protected array $with = [];

    abstract public static function create(array $self): self;

    public function __construct()
    {
        $this->client = app('soql-client');
    }

    public static function query(): ObjectBuilder
    {
        return (new static)->newQuery();
    }

    public function newQuery(): ObjectBuilder
    {
        $builder = new ObjectBuilder($this, new Builder, app('soql-client'));

        $builder
            ->object(static::object())
            ->select(...static::fields());

        return $builder;
    }

    public function getNamespace(): string
    {
        return config('laravel-soql-builder.default_namespace');
    }
}
