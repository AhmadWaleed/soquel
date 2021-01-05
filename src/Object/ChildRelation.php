<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use Illuminate\Support\Str;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryBuilder;

class ChildRelation extends Relationship
{
    public function rfields(): array
    {
        return $this->object::fields();
    }

    public function build(): QueryBuilder
    {
        return $this->object::newQuery()->select(...$this->rfields())->from($this->relation());
    }
}
