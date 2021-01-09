<?php

namespace AhmadWaleed\Soquel\Object;

trait HasRelationship
{
    public function parentRelation(string $object, string $type = 'standard', ?string $relationship = null): ParentRelation
    {
        /** @var BaseObject $object */
        $object = new $object;

        return new ParentRelation($object, $this->builder, $type, $relationship);
    }

    public function childRelation(string $object, string $type = 'standard', ?string $relationship = null): ChildRelation
    {
        /** @var BaseObject $object */
        $object = new $object;

        return new ChildRelation($object, $object->newQuery(), $type, $relationship);
    }
}
