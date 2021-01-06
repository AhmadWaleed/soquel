<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

class ParentRelation extends Relationship
{
    public function rfields(): array
    {
        return collect($this->object::fields())
            ->map(fn (string $field) => "{$this->relation()}.{$field}")
            ->toArray();
    }
}
