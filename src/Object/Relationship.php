<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use Illuminate\Support\Str;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryBuilder;

abstract class Relationship
{
    protected string $type;
    protected string $relation;
    protected SalesForceObject $object;

    abstract public function rfields(): array;

    abstract public function build(): QueryBuilder;

    public function __construct(string $object, string $type = 'standard', ?string $relation = null)
    {
        if (! in_array($type, ['custom', 'standard'])) {
            throw new \InvalidArgumentException("Invalid object type given. valid types (standard, custom)");
        }

        $this->type = $type;
        $this->object = new $object;
        $this->relation = $relation ?: $this->resolveRelationshipName();
    }

    public function resolveRelationshipName(): string
    {
        $object = $this->object::object();

        if ($this->type === 'standard') {
            return $object;
        }

        if (! Str::endsWith($object, '__c')) {
            return $object;
        }

        return str_replace('__c', '__r', $object);
    }

    public function relation(): string
    {
        return $this->relation;
    }

    public function object(): SalesForceObject
    {
        return $this->object;
    }
}
