<?php

namespace AhmadWaleed\LaravelSOQLBuilder\Tests\Unit;

use AhmadWaleed\LaravelSOQLBuilder\Tests\TestCase;
use AhmadWaleed\LaravelSOQLBuilder\Query\QueryBuilder;
use AhmadWaleed\LaravelSOQLBuilder\Tests\Fakes\Client;
use AhmadWaleed\LaravelSOQLBuilder\Tests\Objects\Account;
use AhmadWaleed\LaravelSOQLBuilder\Tests\Objects\Contact;

class QueryBuilderTest extends TestCase
{
    /** @test */
    public function it_select_all_fields_from_object()
    {
        $got = Account::newQuery()->toSOQL();

        $this->assertSame('SELECT Id, Name FROM Account', $got);
    }

    /** @test */
    public function it_only_select_single_fields()
    {
        $builder = Account::newQuery()->select('Id');

        $this->assertSame('SELECT Id FROM Account', $builder->toSOQL());
    }

    /** @test */
    public function it_only_select_multiple_fields()
    {
        $builder = Account::newQuery()->select('Id', 'Name');

        $this->assertSame('SELECT Id, Name FROM Account', $builder->toSOQL());
    }

    /** @test */
    public function it_add_more_fields_to_select()
    {
        $builder = Account::newQuery()->addSelect(['Email']);

        $this->assertSame('SELECT Id, Name, Email FROM Account', $builder->toSOQL());
    }

    /** @test */
    public function it_applies_order_by_clause()
    {
        $builder = Account::newQuery()->orderBy('Name');

        $this->assertSame('SELECT Id, Name FROM Account ORDER BY Name DESC', $builder->toSOQL());
    }

    /** @test */
    public function it_applies_order_by_clause_with_asc_order()
    {
        $builder = Account::newQuery()->orderBy('Name', 'ASC');

        $this->assertSame('SELECT Id, Name FROM Account ORDER BY Name ASC', $builder->toSOQL());
    }

    /** @test */
    public function it_applies_multiple_order_by_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->orderBy('Id')
            ->orderBy('Name', 'ASC')
            ->toSOQL();

        $expected = 'SELECT Id FROM Account ORDER BY Id DESC Name ASC';

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_where_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->where('Name', '=', 'John')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Name = 'John'";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_multiple_where_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->where('Name', '=', 'John')
            ->where('Id', '=', 'aAr3x')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Name = 'John' AND Id = 'aAr3x'";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_multiple_where_clause_with_explicit_condition()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->where('Name', '=', 'John')
            ->where('Name', '=', 'Doe', 'OR')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Name = 'John' OR Name = 'Doe'";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_multiple_or_where_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->where('Name', '=', 'John')
            ->orWhere('Name', '=', 'Doe')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Name = 'John' OR Name = 'Doe'";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_where_in_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->whereIn('Id', ['abc', 'efg'])
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Id IN ('abc', 'efg')";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_where_in_clause_with_sub_query()
    {
        $got = Account::newQuery()->select('Id')
            ->whereIn('Contact.Id', Contact::newQuery()->select('Id'))
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Contact.Id IN (SELECT Id FROM Contact)";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_where_raw_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->whereRaw(
                "DISTANCE(Contact__r.Geolocation__c, GEOLOCATION(15.623,35.949), 'km') < 1000"
            )
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE DISTANCE(Contact__r.Geolocation__c, GEOLOCATION(15.623,35.949), 'km') < 1000";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_where_null_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->whereNull('Fax')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Fax = null";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_multiple_where_null_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->whereNull('Fax')
            ->whereNull('Company')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Fax = null AND Company = null";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_where_not_null_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->whereNotNull('Fax')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Fax != null";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_multiple_where_not_null_clause()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->whereNotNull('Fax')
            ->whereNotNull('Company')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Fax != null AND Company != null";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_add_limit_to_query()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->whereNotNull('Fax')
            ->limit(2)
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Fax != null LIMIT 2";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_query_when_condition_meets()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->when(true, fn (QueryBuilder $builder) => $builder->where('Name', '=', 'John'))
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Name = 'John'";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_will_no_apply_query_if_condition_is_not_true()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->when(false, fn (QueryBuilder $builder) => $builder->where('Name', '=', 'John'))
            ->toSOQL();

        $expected = "SELECT Id FROM Account";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_loads_child_relationship()
    {
        $got = Account::newQuery()
            ->select('Id')
            ->with('contacts')
            ->toSOQL();

        $expected = "SELECT Id, (SELECT Id, Name FROM Contact) FROM Account";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_loads_parent_relationship()
    {
        $got = Contact::newQuery()
            ->select('Id')
            ->with('account')
            ->toSOQL();

        $expected = "SELECT Id, Account.Id, Account.Name FROM Contact";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_loads_parent_and_child_relationship()
    {
        $got = Contact::newQuery()
            ->select('Id')
            ->with('account', 'attachments')
            ->toSOQL();

        $expected = "SELECT Id, Account.Id, Account.Name, (SELECT Id, Name, Content__c FROM Attachments__r) FROM Contact";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_gets_query_records()
    {
        $client = new Client($this->testResponse());

        $this->app->instance('soql-client', $client);

        $objects = $builder = Contact::newQuery()
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

        $object = $builder = Contact::newQuery()
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
