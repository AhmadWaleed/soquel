<?php

namespace AhmadWaleed\Soquel\Tests\Objects;

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
}
