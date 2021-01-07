# [Salesforce Object Query Language (SOQL)](https://developer.salesforce.com/docs/atlas.en-us.soql_sosl.meta/soql_sosl/sforce_api_calls_soql.htm) Package For Laravel

[![Laravel](https://img.shields.io/badge/Laravel-8.0-orange.svg?style=flat-square)](http://laravel.com)

Laravel SOQL query builder provides a convenient, fluent interface to creating [SOQL](https://developer.salesforce.com/docs/atlas.en-us.soql_sosl.meta/soql_sosl/sforce_api_calls_soql.htm) queries and fetching records from salesforce.

## Requirements

- PHP >= 7.4
- Laravel >= 8
- [omniphx/forrest](https://github.com/omniphx/forrest) >= 2.*

> This Package uses [omniphx/forrest](https://github.com/omniphx/forrest) package as salesforce client to fetch records from salesforce, please refer to package github page for installation and configuration guide.

## Requirements
You can install the package via composer:

```bash
composer require ahmedwaleed/laravel-soql-builder
```

## Basic Usage

* Retrieving All Rows From A Object
```php
use AhmadWaleed\LaravelSOQLBuilder\SOQL;

echo SOQL::object('Account')->select('Id', 'Name')->toSOQL();

// Output: SELECT Id, Name FROM Account 
```

* Where Clauses
```php
SOQL::object('Account')->select('Id', 'Name')->where('Id', '=', 's3dty')->toSOQL();
SOQL::object('Account')->select('Id', 'Name')->where('Name', 'Like', '%john%')->toSOQL();
```

* Additional Where Clauses
```php
SOQL::object('Account')->select('Id', 'Name')->where('Id', '=', 's3dty')->orWhere('Id', '=', '2abc')->toSOQL();
SOQL::object('Account')->select('Id', 'Name')->whereIn('Id', ['s3dty', 'ty4ii'])->toSOQL();
SOQL::object('Account')->select('Id', 'Name')->whereNull('Name')->toSOQL();
SOQL::object('Account')->select('Id', 'Name')->whereNotNull('Name')->toSOQL();
SOQL::object('Account')->select('Id', 'Name')->whereRaw("DISTANCE(Contact__r.Geolocation__c, GEOLOCATION(15.623,35.949), 'km') < 1000")->toSOQL();
```

* Select Sub Query
```php
SOQL::object('Account')->select('Id', 'Name')->selectSub(SOQL::object('Contact')->select('Id', 'Name'))->toSOQL();
```

* Subquery Where Clauses
```php
SOQL::object('Account')->select('Id')->whereIn('Id', SOQL::object('Contact')->select('Account.Id'))->toSOQL();
```

* Ordering & Limit
```php
SOQL::object('Account')->select('Id')->orderBy('Id')->toSOQL(); // default order DESC
SOQL::object('Account')->select('Id')->orderBy('Name', 'ACS')->toSOQL();
SOQL::object('Account')->select('Id')->limit(1)->toSOQL();
```

# ORM Usage
Query Builder is good when you want full control over query, but it becomes cumbersome with a query where you need to select all the fields of an object or want to load child pr parent object rows.
This package also provide object-relational-mapper (ORM) support that makes it easy and enjoyable to interact with soql.

### Generate Object Classes
To get started, lets create an Object class which by default lives in `app/Objects` directory and extend the `AhmadWaleed\LaravelSOQLBuilder\Object\BaseObject` class, but you can change the default directory in the configuration file. You may use the `make:object` artisan command to generate a new object class:
```bash
php artisan make:object Account
```

This artisan command by default generate standard object class but if you would like to generate a custom object class you may use the `--custom` or `-c` option:
```bash
php artisan make:object Job --custom
```

The above command will generate following class:
```php
<?php

namespace App\Objects;

use AhmadWaleed\LaravelSOQLBuilder\Object\BaseObject;

class Account extends BaseObject
{
    public string $id;
    public string $name;

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

```

### Retrieving Objects
Once you have created an object, you are ready to start retrieving data from salesforce, You can think of each object class as a powerful query builder allowing you to fluently query salesforce object data.
The get method will retrieve all (limited to 2000 by salesforce) of the records from the associated object.
```php
use App\Objects\Account;

foreach(Account::query()->get() as $account) {
    echo $account->name;
}
```

### Building Queries

Each Object class serves as query builder you add additional constraints to queries and invoke the get method to retrieve the results:
```php
$accounts = Account::query()
                ->where('name', 'LIKE', '%john%')
                ->limit(10)
                ->get();
```

### Collections
As we have seen, Object method like get retrieve multiple records from the database. However, these methods don't return a plain PHP array. Instead, an instance of Illuminate\Database\Eloquent\Collection is returned.

The Object class extends Laravel's base Illuminate\Support\Collection class, which provides a variety of helpful methods for interacting with data collections. For example, the reject method may be used to remove objects from a collection based on the results of an invoked closure
```php
$accounts = Account::query()->whereNotNull('Email')->get();
$accounts = $accounts->reject(fn (Account $account) => $account->isActive);
```
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
