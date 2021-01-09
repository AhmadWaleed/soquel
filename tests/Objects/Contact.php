<?php

namespace AhmadWaleed\Soquel\Tests\Objects;

use AhmadWaleed\Soquel\Object\BaseObject;
use AhmadWaleed\Soquel\Object\ChildRelation;
use AhmadWaleed\Soquel\Object\ObjectBuilder;
use AhmadWaleed\Soquel\Object\ParentRelation;

/** @mixin ObjectBuilder */
class Contact extends BaseObject
{
    public string $id;
    public string $name;

    public function account(): ParentRelation
    {
        return $this->parentRelation(Account::class);
    }

    public function attachments(): ChildRelation
    {
        return $this->childRelation(Attachment::class, 'custom', 'Attachments__r');
    }

    public function toArray(): array
    {
        return [
            'Id' => $this->id,
            'Name' => $this->name,
        ];
    }

    public static function object(): string
    {
        return 'Contact';
    }

    public static function fields(): array
    {
        return [
            'Id',
            'Name',
        ];
    }

    public static function create(array $object): BaseObject
    {
        $self = new static();

        $self->id = $object['Id'];
        $self->name = $object['Name'];

        return $self;
    }
}
