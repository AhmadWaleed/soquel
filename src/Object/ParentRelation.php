<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use AhmadWaleed\LaravelSOQLBuilder\Query\QueryBuilder;

class ParentRelation extends Relationship
{
    public function rfields(): array
    {
        return collect($this->object::fields())
            ->map(fn (string $field) => "{$this->relation()}.{$field}")
            ->toArray();
    }

    public function build(): QueryBuilder
    {
        return $this->object::newQuery()->select(...$this->rfields());
    }
}
