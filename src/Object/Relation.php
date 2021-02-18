<?php

namespace AhmadWaleed\Soquel\Object;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;

abstract class Relation
{
    use ForwardsCalls;

    protected string $type;
    protected BaseObject $model;
    protected string $relationship;
    protected ObjectBuilder $builder;

    public function __construct(BaseObject $object, ObjectBuilder $builder, string $type = 'standard', ?string $relationship = null)
    {
        if (! in_array($type, ['custom', 'standard'])) {
            throw new \InvalidArgumentException("Invalid object type given. valid types (standard, custom)");
        }

        $this->type = $type;
        $this->model = $object;
        $this->builder = $builder;
        $this->relationship = $relationship ?: $this->resolveRelationshipName();
    }

    public function resolveRelationshipName(): string
    {
        $object = $this->model->sobject();

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
        return $this->model;
    }

    public function rfields(): array
    {
        if ($this instanceof ParentRelation) {
            return collect($this->model->fields())
                ->map(fn (string $field) => "{$this->robject()}.{$field}")
                ->toArray();
        }

        return $this->model->fields();
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

    public function getRelationship(): string
    {
        return $this->relationship;
    }

    public function getModel(): BaseObject
    {
        return $this->model;
    }
}
