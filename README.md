# [Salesforce Object Query Builder (SOQL)](https://developer.salesforce.com/docs/atlas.en-us.soql_sosl.meta/soql_sosl/sforce_api_calls_soql.htm) Package For Laravel

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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.