<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests\Objects;

use AhmadWaleed\LaravelSOQLBuilder\Object\BaseObject;
use AhmadWaleed\LaravelSOQLBuilder\Object\ChildRelation;
use AhmadWaleed\LaravelSOQLBuilder\Object\ObjectBuilder;
use AhmadWaleed\LaravelSOQLBuilder\Object\ParentRelation;

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
