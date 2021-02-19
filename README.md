# [Salesforce Object Query Language (SOQL)](https://developer.salesforce.com/docs/atlas.en-us.soql_sosl.meta/soql_sosl/sforce_api_calls_soql.htm) Package For Laravel

[![Laravel](https://img.shields.io/badge/Laravel-8.0-orange.svg?style=flat-square)](http://laravel.com)

Laravel SOQL query builder provides a convenient, fluent interface to creating, updating, [SOQL](https://developer.salesforce.com/docs/atlas.en-us.soql_sosl.meta/soql_sosl/sforce_api_calls_soql.htm) queries and fetching records from salesforce.

## Requirements

- PHP >= 7.4
- Laravel >= 8
- [omniphx/forrest](https://github.com/omniphx/forrest) >= 2.*

> This Package uses [omniphx/forrest](https://github.com/omniphx/forrest) package as salesforce client to fetch records from salesforce, please refer to package github page for installation and configuration guide.

## Installation
You can install the package via composer:

```bash
composer require ahmedwaleed/soquel
```

Optionally, you can publish the config file of the package.

```php
php artisan vendor:publish --provider="AhmadWaleed\Soquel\SoquelServiceProvider"
```
Set following config with your salesforce credentials.
```php
'credentials' => [
    // Required:
    'consumerKey' => env('SF_CONSUMER_KEY'),
    'consumerSecret' => env('SF_CONSUMER_SECRET'),
    'callbackURI' => env('SF_CALLBACK_URI'),
    'loginURL' => env('SF_LOGIN_URL'),

    // Only required for UserPassword authentication:
    'username' => env('SF_USERNAME'),
    // Security token might need to be ammended to password unless IP Address is whitelisted
    'password' => env('SF_PASSWORD'),
]

```

If you need to modify any more settings for Forrest, publish the config file using the artisan command:
```bash
php artisan vendor:publish
```
## Basic Usage

The content of config file will be published at `config/soquel.php`

* Retrieving All Rows From A Object
```php
use AhmadWaleed\Soquel\SOQL;

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
Query Builder is good when you want full control over query, but it becomes cumbersome with a query where you need to 
select all the fields of an object or want to load child pr parent object rows. This package also provide 
object-relational-mapper (ORM) support that makes it easy and enjoyable to interact with soql.

### Generate Object Classes
To get started, lets create an Object class which by default lives in `app/Objects` directory and extend 
the `AhmadWaleed\Soquel\Object\BaseObject` class, but you can change the default directory in the configuration file. 
You may use the `make:object` artisan command to generate a new object class:
```bash
php artisan make:object Account
```

The above command will generate following class:
```php
<?php

namespace App\Objects;

use AhmadWaleed\Soquel\Object\BaseObject;

class Account extends BaseObject
{
}

```

If you would like to generate a custom object class you may use the `--type` or `-t` option:
```bash
php artisan make:object Job --type=custom
```

The above command will generate following class:
```php
<?php

namespace App\Objects;

use AhmadWaleed\Soquel\Object\BaseObject;

class Job extends BaseObject
{
    protected string $sobject = 'Job__c';
}

```
By default, the Id field of salesforce object will be retrieved, You can also define on the model what fields you want to bring back with each record.
```php
public array $fields = [
    'Name',
    'Email',
];
```

By default, the Id filed is readonly, You can specify which fileds should be excluded while performing insert/update operation on model.

```php
protected array $readOnly = ['Name'];
```

### Retrieving Objects
Once you have created an object, you are ready to start retrieving data from salesforce, You can think of each 
object class as a powerful query builder allowing you to fluently query salesforce object data. The get method will 
retrieve all (limited to 2000 by salesforce) of the records from the associated object.
```php
use App\Objects\Account;

foreach(Account::query()->get() as $account) {
    echo $account->name;
}
```

### Building Queries

Each Object class serves as query builder you add additional constraints to queries and invoke the get method to 
retrieve the results:
```php
$accounts = Account::query()
                ->where('name', 'LIKE', '%john%')
                ->limit(10)
                ->get();
```

### Collections
As we have seen, Object method like get retrieve multiple records from the salesforce. However, these methods don't 
return a plain PHP array. Instead, an instance of Illuminate\Database\Eloquent\Collection is returned.

The Object class extends Laravel's base Illuminate\Support\Collection class, which provides a variety of helpful methods
for interacting with data collections. For example, the reject method may be used to remove objects from a collection 
based on the results of an invoked closure
```php
$accounts = Account::query()->whereNotNull('Email')->get();
$accounts = $accounts->reject(fn (Account $account) => $account->isActive);
```

### Relationships
Salesforce's objects are often related to one another. For example, a Account may have many Contacts, or an Contact 
could be related to the Account. SOQL ORM makes managing and working with these relationships easy, and supports parent 
and child relationships:

### Defining Relationships
  
Relationships are defined as methods on your Object classes. Since relationships also serve as powerful query builders, 
defining relationships as methods provides powerful method chaining and querying capabilities. For example, we may chain
additional query constraints on this contacts relationship:

```php
$account->contacts()->where('Name', 'LIKE', '%john%');
```
But, before diving too deep into using relationships, let's learn how to define each type of relationship.

### Child To Parent

A child-to-parent relationship is a very basic type of salesforce relationship. For example, a Contact object might be associated with one Account object. To define this relationship, we will place a account method on the Contact object. The account method should call the parentRelation method and return its result. The parentRelation method is available to your model via the object AhmadWaleed\Soquel\Object\BaseObject base class:

```php
<?php

namespace AhmadWaleed\Soquel\Tests\Objects;

use AhmadWaleed\Soquel\Object\BaseObject;
use AhmadWaleed\Soquel\Object\ParentRelation;

class Contact extends BaseObject
{
    public Account $account;
    
    public function account(): ParentRelation
    {
        return $this->parentRelation(Account::class);
    }
}
```
The first argument passed to the parentRelation method is the name of the related object class. Once the relationship 
is defined, we may retrieve the related record with following query:
```php
$account = Contact::new()->query()->with('account')->find('id')->account;
```

Additionally, you can pass object type and relationship name in second and third argument to parentRelation method;
```php
return $this->parentRelation(Job::class, 'Job__c');
```
For custom objects orm assumes relationship name, For example for custom object Job__c the relationship name will be 
Job__r, But if you want to override the default convention you can pass relationship name as third argument.
```php
return $this->parentRelation(Job::class, 'Job__c', 'jobs');
```

### Parent To Child
  
A parent-to-child relationship is used to define relationships where a single object is the parent to one or more child 
objects. For example, a account may have an infinite number of contacts. Like all other Salesforce relationships, 
parent-to-child relationships are defined by defining a method on your Object class:

```php
<?php

namespace AhmadWaleed\Soquel\Tests\Objects;

use AhmadWaleed\Soquel\Object\BaseObject;
use AhmadWaleed\Soquel\Object\ChildRelation;
use Illuminate\Support\Collection;

class Account extends BaseObject
{
    public Collection $contacts;
    
    public function contacts(): ChildRelation
    {
        return $this->childRelation(Contact::class);
    }
}
```
The first argument passed to the childRelation method is the name of the related object class. Once the relationship is 
defined, we may retrieve the related records with following query:
```php
$contacts = Account::new()->query()->with('contacts')->find('id')->contacts;
```

Additionally, you can pass object type and relationship name in second and third argument to childRelation method;
```php
return $this->childRelation(Attachment::class, 'Attachment__c');
```
For custom objects orm assumes relationship name, For example for custom object Attachment__c the relationship name will
be Attachment__r, But if you want to override the default convention you can pass relationship name as third argument.
```php
return $this->childRelation(Attachment::class, 'Attachment__c', 'attachments');
```

### Inserting and Updating

- Insert
```php
$contact = new Contact();
$contact->LastName = 'doe';
$contact->Email = 'john@example.com';
$contact->save();

// OR 

$contact = Contact::create(['LastName' => 'doe', 'Email', 'john@example.com']);
```

- Insert Collection
```php
$contacts = collect([
	new Contact(['Email' => 'email1@test.com']),
	new Contact(['Email' => 'email2@test.com'])
]);

Contact::saveMany($contacts);

// OR

$contacts = collect([
	['Email' => 'email1@test.com'],
	['Email' => 'email2@test.com']
]);

Contact::saveMany($contacts);
```

- Update
```php
$contact = new Contact();
$contact->LastName = 'michael';
$contact->save();

// OR 

$contact = $contact->update(['LastName' => 'michael', 'Email', 'michael@example.com']);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
