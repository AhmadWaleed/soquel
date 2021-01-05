<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests\Objects;

use AhmadWaleed\LaravelSOQLBuilder\Object\Relationship;
use AhmadWaleed\LaravelSOQLBuilder\Object\ParentRelation;
use AhmadWaleed\LaravelSOQLBuilder\Object\SalesForceObject;

class Attachment extends SalesForceObject
{
    public string $id;
    public string $name;

    public function contact(): Relationship
    {
        return new ParentRelation(Contact::class);
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
        return 'Attachment';
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
        $self = new self();

        $self->id = $object['Id'];
        $self->name = $object['Name'];

        return $self;
    }
}
