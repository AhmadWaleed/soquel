<?php

namespace AhmadWaleed\Soquel\Tests\Objects;

use Carbon\Carbon;
use AhmadWaleed\Soquel\Object\BaseObject;
use AhmadWaleed\Soquel\Object\ChildRelation;

class Account extends BaseObject
{
    protected array $fields = [
        'Name',
    ];

    public function contacts(): ChildRelation
    {
        return $this->childRelation(Contact::class);
    }

    public function setDateAttribute($value): Carbon
    {
        return Carbon::parse($value);
    }

    public function getCreatedAtAttribute($value): Carbon
    {
        return Carbon::parse($value);
    }
}
