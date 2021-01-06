<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use AhmadWaleed\LaravelSOQLBuilder\Query\Builder;

class ChildRelation extends Relationship
{
    public function rfields(): array
    {
        return $this->object::fields();
    }

    public function build(): Builder
    {
        return $this->object::newQuery()->select(...$this->rfields())->from($this->relation());
    }
}
