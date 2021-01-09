<?php

namespace AhmadWaleed\Soquel\Object;

/** @mixin ObjectBuilder */
class ParentRelation extends Relation
{
    public function __construct(BaseObject $object, ObjectBuilder $builder, string $type = 'standard', ?string $relationship = null)
    {
        parent::__construct($object, $builder, $type, $relationship);

        $this->builder->addSelect(...$this->rfields());
    }
}
