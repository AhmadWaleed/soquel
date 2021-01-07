<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;

abstract class Relation
{
    use ForwardsCalls;

    protected string $type;
    protected BaseObject $object;
    protected string $relationship;
    protected ObjectBuilder $builder;

    public function __construct(BaseObject $object, ObjectBuilder $builder, string $type = 'standard', ?string $relationship = null)
    {
        if (! in_array($type, ['custom', 'standard'])) {
            throw new \InvalidArgumentException("Invalid object type given. valid types (standard, custom)");
        }

        $this->type = $type;
        $this->object = $object;
        $this->builder = $builder;
        $this->relationship = $relationship ?: $this->resolveRelationshipName();
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

    public function object(): BaseObject
    {
        return $this->object;
    }

    public function rfields(): array
    {
        if ($this instanceof ParentRelation) {
            return collect($this->object::fields())
                ->map(fn (string $field) => "{$this->robject()}.{$field}")
                ->toArray();
        }

        return $this->object::fields();
    }

    public function robject(): ?string
    {
        return $this->relationship;
    }

    public function __call(string $method, array $parameters): ObjectBuilder
    {
        return $this->forwardCallTo($this->builder, $method, $parameters);
    }

    public function getBuilder(): ObjectBuilder
    {
        return $this->builder;
    }
}
