<?php

namespace AhmadWaleed\Soquel\Tests\Objects;

use AhmadWaleed\Soquel\Object\BaseObject;
use AhmadWaleed\Soquel\Object\ParentRelation;

class Attachment extends BaseObject
{
    public string $id;
    public string $name;
    public string $content;

    public function contact(): ParentRelation
    {
        return $this->parentRelation(Contact::class);
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

    public static function create(array $object): BaseObject
    {
        $self = new self();

        $self->id = $object['Id'];
        $self->name = $object['Name'];
        $self->content = $object['Content__c'];

        return $self;
    }
}
