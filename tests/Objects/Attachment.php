<?php

namespace AhmadWaleed\Soquel\Tests\Objects;

use AhmadWaleed\Soquel\Object\BaseObject;
use AhmadWaleed\Soquel\Object\ParentRelation;

class Attachment extends BaseObject
{
    protected array $fields = [
        'Name',
        'Contact__c',
    ];

    public function contact(): ParentRelation
    {
        return $this->parentRelation(Contact::class);
    }
}
