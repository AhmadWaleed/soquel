<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests\Objects;

use AhmadWaleed\LaravelSOQLBuilder\Object\Relationship;
use AhmadWaleed\LaravelSOQLBuilder\Object\ParentRelation;
use AhmadWaleed\LaravelSOQLBuilder\Object\SalesForceObject;

class Attachment extends SalesForceObject
{
    public string $id;
    public string $name;
    public string $content;

    public function contact(): Relationship
    {
        return new ParentRelation(Contact::class);
    }

    public function toArray(): array
    {
        return [
            'Id' => $this->id,
            'Name' => $this->name,
            'Content__c' => $this->content,
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
            'Content__c',
        ];
    }

    public static function create(array $object): SalesForceObject
    {
        $self = new self();

        $self->id = $object['Id'];
        $self->name = $object['Name'];
        $self->content = $object['Content__c'];

        return $self;
    }
}
