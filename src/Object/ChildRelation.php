<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

/** @mixin ObjectBuilder */
class ChildRelation extends Relation
{
    public function __construct(BaseObject $object, ObjectBuilder $builder, string $type = 'standard', ?string $relationship = null)
    {
        parent::__construct($object, $builder, $type, $relationship);

        $this->builder->object($this->robject())->select(...$this->rfields());
    }
}
