<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use AhmadWaleed\LaravelSOQLBuilder\SOQL;
use AhmadWaleed\LaravelSOQLBuilder\Query\Builder;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryableInterface;
use AhmadWaleed\LaravelSOQLBuilder\Tests\Objects\ObjectBuilder;

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

    private function addRelation(string $relation): self
    {
        if (! method_exists($this, $relation)) {
            throw new \Exception($relation.' does not exist on '.get_class($this));
        }

        $relationship = $this->{$relation}();

        if (! $relationship instanceof Relationship) {
            throw new \UnexpectedValueException('Relationship method must return instance of '.Relationship::class);
        }

        if ($relationship instanceof ParentRelation) {
            $this->query()->addSelect(...$relationship->rfields());
        }

        if ($relationship instanceof ChildRelation) {
            $this->query()->selectSub(
                SOQL::object($relationship->object())->select(...$relationship->rfields())
            );
        }

        return $relationship->object();
    }
}
