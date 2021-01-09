<?php

namespace AhmadWaleed\Soquel\Tests\Unit;

use AhmadWaleed\Soquel\Tests\TestCase;
use AhmadWaleed\Soquel\Tests\Fakes\Client;
use AhmadWaleed\Soquel\Tests\Objects\Account;
use AhmadWaleed\Soquel\Tests\Objects\Contact;

class ObjectTest extends TestCase
{
    /** @test */
    public function it_query_objects()
    {
        $got = Account::new()->query()
            ->select('Id', 'Name')
            ->where('Name', 'LIKE', '%John%')
            ->orderBy('Id')
            ->toSOQL();

        $expected = "SELECT Id, Name FROM Account WHERE Name LIKE '%John%' ORDER BY Id DESC";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_eager_load_parent_relationship()
    {
        $got = Contact::new()->query()
            ->with('account')
            ->toSOQL();

        $expected = "SELECT Id, Name, Account.Id, Account.Name FROM Contact";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_eager_load_child_relationship()
    {
        $got = Account::new()->query()
            ->select('Id')
            ->with('contacts')
            ->toSOQL();

        $expected = "SELECT Id, (SELECT Id, Name FROM Contact) FROM Account";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_query_constraints_on_parent_relationship()
    {
        $got = Contact::new()->account()->whereNotNull('Account.Email')->toSOQL();

        $expected = "SELECT Id, Name, Account.Id, Account.Name FROM Contact WHERE Account.Email != null";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_query_constraints_on_child_relationship()
    {
        $got = Account::new()->contacts()->whereNotNull('Email')->toSOQL();

        $expected = "SELECT Id, Name FROM Contact WHERE Email != null";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_eager_loads_parent_and_child_relationship()
    {
        $got = Contact::new()->query()
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

        $objects = Contact::new()->query()
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

        $object = $builder = Contact::new()
            ->query()
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
