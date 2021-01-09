<?php

namespace AhmadWaleed\Soquel\Tests\Unit;

use AhmadWaleed\Soquel\SOQL;
use AhmadWaleed\Soquel\Query\Builder;
use AhmadWaleed\Soquel\Tests\TestCase;

class QueryBuilderTest extends TestCase
{
    /** @test */
    public function it_select_all_fields_from_object()
    {
        $soql = SOQL::object('Account')->select('Id', 'Name')->toSOQL();

        $this->assertSame('SELECT Id, Name FROM Account', $soql);
    }

    /** @test */
    public function it_only_select_single_fields()
    {
        $soql = SOQL::object('Account')->select('Id')->toSOQL();

        $this->assertSame('SELECT Id FROM Account', $soql);
    }

    /** @test */
    public function it_only_select_multiple_fields()
    {
        $soql = SOQL::object('Account')->select('Id', 'Name')->toSOQL();

        $this->assertSame('SELECT Id, Name FROM Account', $soql);
    }

    /** @test */
    public function it_add_more_fields_to_select()
    {
        $soql = SOQL::object('Account')->select('Id', 'Name')->addSelect('Email')->toSOQL();

        $this->assertSame('SELECT Id, Name, Email FROM Account', $soql);
    }

    /** @test */
    public function it_applies_order_by_clause()
    {
        $soql = SOQL::object('Account')->select('Id', 'Name')->orderBy('Name')->toSOQL();

        $this->assertSame('SELECT Id, Name FROM Account ORDER BY Name DESC', $soql);
    }

    /** @test */
    public function it_applies_order_by_clause_with_asc_order()
    {
        $soql = SOQL::object('Account')
            ->select('Id', 'Name')
            ->orderBy('Name', 'ASC')
            ->toSOQL();

        $this->assertSame('SELECT Id, Name FROM Account ORDER BY Name ASC', $soql);
    }

    /** @test */
    public function it_applies_multiple_order_by_clause()
    {
        $got = SOQL::object('Account')
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
        $got = SOQL::object('Account')
            ->select('Id')
            ->where('Name', '=', 'John')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Name = 'John'";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_multiple_where_clause()
    {
        $got = SOQL::object('Account')
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
        $got = SOQL::object('Account')
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
        $got = SOQL::object('Account')
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
        $got = SOQL::object('Account')
            ->select('Id')
            ->whereIn('Id', ['abc', 'efg'])
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Id IN ('abc', 'efg')";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_where_in_clause_with_sub_query()
    {
        $got = SOQL::object('Account')->select('Id')
            ->whereIn('Contact.Id', SOQL::object('Contact')->select('Id'))
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Contact.Id IN (SELECT Id FROM Contact)";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_where_raw_clause()
    {
        $got = SOQL::object('Account')
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
        $got = SOQL::object('Account')
            ->select('Id')
            ->whereNull('Fax')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Fax = null";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_multiple_where_null_clause()
    {
        $got = SOQL::object('Account')
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
        $got = SOQL::object('Account')
            ->select('Id')
            ->whereNotNull('Fax')
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Fax != null";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_applies_multiple_where_not_null_clause()
    {
        $got = SOQL::object('Account')
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
        $got = SOQL::object('Account')
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
        $got = SOQL::object('Account')
            ->select('Id')
            ->when(true, fn (Builder $builder) => $builder->where('Name', '=', 'John'))
            ->toSOQL();

        $expected = "SELECT Id FROM Account WHERE Name = 'John'";

        $this->assertSame($expected, $got);
    }

    /** @test */
    public function it_will_no_apply_query_if_condition_is_not_true()
    {
        $got = SOQL::object('Account')
            ->select('Id')
            ->when(false, fn (Builder $builder) => $builder->where('Name', '=', 'John'))
            ->toSOQL();

        $expected = "SELECT Id FROM Account";

        $this->assertSame($expected, $got);
    }
}
