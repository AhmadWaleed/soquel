<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use Illuminate\Support\Traits\ForwardsCalls;
use AhmadWaleed\LaravelSOQLBuilder\Query\Builder;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryableInterface;

abstract class BaseObject implements ObjectInterface
{
    use HasRelationship, ForwardsCalls;

    protected ObjectBuilder $builder;
    protected QueryableInterface $client;
    protected array $with = [];

    abstract public static function create(array $self): self;

    public function __construct()
    {
        $this->client = app('soql-client');
        $this->builder = $this->newQuery();
    }

    public static function new(?string $object = null): self
    {
        return new static();
    }

    public function query(): ObjectBuilder
    {
        return $this->builder;
    }

    public function newQuery(?string $object = null): ObjectBuilder
    {
        $this->builder = new ObjectBuilder($this, new Builder, app('soql-client'));

        $this->builder
            ->object($object ?: static::object())
            ->select(...static::fields());

        return $this->builder;
    }

    public function getNamespace(): string
    {
        return config('laravel-soql-builder.default_namespace');
    }

    /** @return mixed */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->builder, $method, $parameters);
    }
}
