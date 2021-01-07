<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests\Objects;

use AhmadWaleed\LaravelSOQLBuilder\Object\BaseObject;
use AhmadWaleed\LaravelSOQLBuilder\Object\ChildRelation;

class Account extends BaseObject
{
    public string $id;
    public string $name;

    public function contacts(): ChildRelation
    {
        return $this->childRelation(Contact::class);
    }

    /**
     * Returns object fields names mapped with values
     */
    public function toArray(): array
    {
        return [
            'Id' => $this->id,
            'Name' => $this->name,
        ];
    }

    /**
     * Returns object name
     */
    public static function object(): string
    {
        return 'Account';
    }

    /**
     * Returns object fields
     */
    public static function fields(): array
    {
        return [
            'Id',
            'Name',
        ];
    }

    /**
     * Create object class from salesforce response
     */
    public static function create(array $object): BaseObject
    {
        $self = new self();

        $self->id = $object['Id'];
        $self->name = $object['Name'];

        return $self;
    }
}
