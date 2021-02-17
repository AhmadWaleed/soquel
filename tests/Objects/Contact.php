<?php

namespace AhmadWaleed\Soquel\Tests\Objects;

use AhmadWaleed\Soquel\Object\BaseObject;
use AhmadWaleed\Soquel\Object\ChildRelation;
use AhmadWaleed\Soquel\Object\ObjectBuilder;
use AhmadWaleed\Soquel\Object\ParentRelation;

/** @mixin ObjectBuilder */
class Contact extends BaseObject
{
    protected array $fields = ['Name'];

    public function account(): ParentRelation
    {
        return $this->parentRelation(Account::class);
    }

    public function attachments(): ChildRelation
    {
        return $this->childRelation(Attachment::class, 'custom', 'Attachments__r');
    }
}
