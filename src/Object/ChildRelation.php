<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Object;

use Illuminate\Support\Str;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryBuilder;

class ChildRelation extends Relationship
{
    public function rfields(): array
    {
        return collect($this->object::fields())->map(function (string $field) {
            if (! Str::endsWith($field, '__c')) {
                return $field;
            }

            return str_replace('__c', '__r', $field);
        })->toArray();
    }

    public function build(): QueryBuilder
    {
        return $this->object::newQuery()->select(...$this->rfields())->from($this->relation());
    }
}
