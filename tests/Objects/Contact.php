<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests\Objects;

use AhmadWaleed\LaravelSOQLBuilder\Object\Relationship;
use AhmadWaleed\LaravelSOQLBuilder\Object\ChildRelation;
use AhmadWaleed\LaravelSOQLBuilder\Object\ParentRelation;
use AhmadWaleed\LaravelSOQLBuilder\Object\SalesForceObject;

class Contact extends SalesForceObject
{
    public string $id;
    public string $name;

    public function account(): Relationship
    {
        return new ParentRelation(Account::class);
    }

    public function attachments(): Relationship
    {
        return new ChildRelation(Attachment::class, 'custom', 'Attachments__r');
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

    public static function create(array $object): SalesForceObject
    {
        $self = new static();

        $self->id = $object['Id'];
        $self->name = $object['Name'];

        return $self;
    }
}
