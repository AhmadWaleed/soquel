<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests\Unit;

use AhmadWaleed\LaravelSOQLBuilder\Tests\TestCase;
use AhmadWaleed\LaravelSOQLBuilder\Tests\Fakes\Client;
use AhmadWaleed\LaravelSOQLBuilder\Tests\Objects\Account;
use AhmadWaleed\LaravelSOQLBuilder\Tests\Objects\Contact;

class ObjectTest extends TestCase
{
    /** @test */
    public function it_query_objects()
    {
        $got = Account::query()
            ->select('Id', 'Name')
            ->where('Name', 'LIKE', '%John%')
            ->orderBy('Id')
            ->toSOQL();

        $expected = "SELECT Id, Name FROM Account WHERE Name LIKE '%John%' ORDER BY Id DESC";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_loads_parent_relationship()
    {
        $got = Contact::query()
            ->with('account')
            ->toSOQL();

        $expected = "SELECT Id, Name, Account.Id, Account.Name FROM Contact";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_loads_child_relationship()
    {
        $got = Account::query()
            ->select('Id')
            ->with('contacts')
            ->toSOQL();

        $expected = "SELECT Id, (SELECT Id, Name FROM Contact) FROM Account";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_loads_parent_and_child_relationship()
    {
        $got = Contact::query()
            ->with('account', 'attachments')
            ->toSOQL();

        $expected = "SELECT Id, Name, Account.Id, Account.Name, (SELECT Id, Name, Content__c FROM Attachments__r) FROM Contact";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_gets_query_records()
    {
        $client = new Client($this->testResponse());

        $this->app->instance('soql-client', $client);

        $objects = Contact::query()
            ->select('Id')
            ->get();

        $this->assertCount(2, $objects);
        foreach ($objects as $object) {
            $this->assertInstanceOf(Contact::class, $object);
        }
    }

    /** @test */
    public function it_gets_first_record()
    {
        $client = new Client($this->testResponse());

        $this->app->instance('soql-client', $client);

        $object = $builder = Contact::query()
            ->select('Id')
            ->first();

        $this->assertInstanceOf(Contact::class, $object);
    }

    private function testResponse(): array
    {
        return [
            [
                'Id' => 'av2t',
                'Name' => 'John Doe',
            ],
            [
                'Id' => 'avf7gt',
                'Name' => 'Michael Pit',
            ],
        ];
    }
}
