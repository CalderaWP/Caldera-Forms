[![Build Status](https://travis-ci.org/calderawp/caldera-forms-query.svg?branch=master)](https://travis-ci.org/calderawp/caldera-forms-query)

This library provides for developer-friendly ways to query for or delete Caldera Forms entry data.

## Why?
* [To provide the types of queries we need for reporting and deleting data in order to add GDPR compliance to Caldera Forms](https://github.com/CalderaWP/Caldera-Forms/issues/2108)
* To provide the types of queries we need for improving Caldera Forms features such as entry viewer, entry export, entry editing and Connected Forms.

## Install
`composer require calderawp/caldera-forms-query`

## Requires
* WordPress - tested with 4.8, latest and trunk
* PHP 5.6+ - tested with PHP 7.1 and 7.2
* Caldera Forms 1.6.0+ - tested with Caldera Forms 1.6.1 beta 1

## Status
* Works
* Does not yet select/delete by date range
* **Prepared SQL needs to be sanitized better.**
## Usage


### Basic Queries
```php
/**
 * Examples of simple queries
 *
 * Using the class: \calderawp\CalderaFormsQuery\Features\FeatureContainer
 * Via the static accessor function: calderawp\CalderaFormsQueries\CalderaFormsQueries()
 */

/** First make the function usable without a full namespace */
use function calderawp\CalderaFormsQueries\CalderaFormsQueries;

/** Do Some Queries */
//Select all data by user ID
$entries = CalderaFormsQueries()->selectByUserId(42);

//Select all entries that have a field whose slug is "email" and the value of that field's value is "delete@please.eu"
$entries = CalderaFormsQueries()->selectByFieldValue( 'email', 'delete@please.eu' );

//Select all entries that do not have field whose slug is "size" and the value of that field's value is "big"
$entries = CalderaFormsQueries()->selectByFieldValue( 'size', 'big', false );

//Delete all data by Entry ID
CalderaFormsQueries()->deleteByEntryIds([1,1,2,3,5,8,42]);

//Delete all data by User ID
CalderaFormsQueries()->deleteByUserId(42);
```

### Paginated Queries
The selectByFieldValue feature method defaults to limiting queries to 25. You can set the page and limit with the 4th & 5th arguments.
```php
/**
 * Examples of simple queries
 *
 * Using the class: \calderawp\CalderaFormsQuery\Features\FeatureContainer
 * Via the static accessor function: calderawp\CalderaFormsQueries\CalderaFormsQueries()
 */

/** First make the function usable without a full namespace */
use function calderawp\CalderaFormsQueries\CalderaFormsQueries;

/** Do Some Queries */
//Select all entries that have a field whose slug is "email" and the value of that field's value is "delete@please.eu"
//The first 25 entries
$entries = CalderaFormsQueries()->selectByFieldValue( 'email', 'delete@please.eu' );
//The second 25 entries
$entries = CalderaFormsQueries()->selectByFieldValue( 'email', 'delete@please.eu', true, 2 );
//Get 5th page, with 50 results per page
$entries = CalderaFormsQueries()->selectByFieldValue( 'email', 'delete@please.eu', true, 5, 50 );
```

## Constructing Other Queries
The feature container provides helper methods that allow for simple queries like those listed above. It also exposes the underlying query generators. 

You can access any of the generators using the `getQueries()` method. For example:

```php
 $featureContainer = \calderawp\CalderaFormsQueries\CalderaFormsQueries();
    $fieldValue = 'X@x.com';
    $formId = 'CF5afb00e97d698';
    $count = Caldera_Forms_Entry_Bulk::count($formId );

    $entrySelector = $featureContainer
        ->getQueries()
        ->entrySelect();
```

#### `is()` Helper Method
This is a more complete example showing a selection of entry values where the field with the slug `primary_email` is `roy@hiroy.club` and the field with the slug of `first_name` is `Mike`. It is also using the `is()` method to add WHERE statements, as well as the `addPagination()` method to query for the second page of results with 50 results per page.

```php
    $featureContainer = \calderawp\CalderaFormsQueries\CalderaFormsQueries();
    $entrySelector = $featureContainer
        ->getQueries()
        ->entrySelect()
        ->is( 'primary_email', 'roy@hiroy.club' )
        ->is( 'first_name', 'Mike' )
        ->addPagination(2,50 );
```

#### `in()` Helper Method
This example shows selection of all entry values where the entry ID is in an array of entry IDs.

```php
    $featureContainer = \calderawp\CalderaFormsQueries\CalderaFormsQueries();
    $entrySelector = $featureContainer
        ->getQueries()
        ->entrySelect()
        ->in( 'entry_id', [ 42, 3 ] );
```

### Query Generators
All query generators extend the `\calderawp\CalderaFormsQuery\QueryBuilder` class and impairment `\calderawp\CalderaFormsQuery\CreatesSqlQueries`.

Query generators are responsible for creating SQL queries. They do not perform sequel queries.
#### Select Query Generators
Select query generators extend `\calderawp\CalderaFormsQuery\Select\SelectQueryBuilder` and impliment `\calderawp\CalderaFormsQuery\Select\DoesSelectQuery` and `\calderawp\CalderaFormsQuery\Select\DoesSelectQueryByEntryId`. 

#### Useful Methods of `SelectQueryBuilder`s

* `in()`


### Using Query Generators To Perform SQL Queries

#### SELECT
The `getQueries()` method of the `FeatureContainer` returns a `calderawp\CalderaFormsQuery\Features\Queries` instance. This provides us with a `select` method when passed a `SelectQueryBuilder` returns an array of `stdClass` object of results.


```php
        $featureContainer = \calderawp\CalderaFormsQueries\CalderaFormsQueries();
        $entryValueSelect = $featureContainer
            ->getQueries()
            ->entryValuesSelect()
            ->is( 'size', 'large' );

       $featureContainer->getQueries()->select( $entryValueSelect );
```

You can also access the generated SQL as a string.

```php

  $featureContainer = \calderawp\CalderaFormsQueries\CalderaFormsQueries();
        $sql = $featureContainer
            ->getQueries()
            ->entryValuesSelect()
            ->is( 'size', 'large' )
            ->getPreparedSql();
```

#### DELETE
The `Queries` class also has a `delete` method we can pass a `DeleteQueryBuilder` to perform a DELETE query against the database.


## Development
### Install
Requires git and Composer

* `git clone git@github.com:calderawp/caldera-forms-query.git`
* `cd caldera-forms-query`
* `composer install`

### Local Development Environment
A  local development environment is included, and provided. It is used for integration tests. Requires Composer, Docker and Docker Compose.

* Install Local Environment And WordPress "Unit" Test Suite
- `composer wp-install`

You should know have WordPress at http://localhost:8888/

* (re)Start Server: Once server is installed, you can start it again
- `composer wp-start`

### Testing

#### Install
Follow the steps above to create local development environment, then you can use the commands listed in the next section.

#### Use
Run these commands from the plugin's root directory.

* Run All Tests and Code Sniffs and Fixes
    - `composer tests`
* Run Unit Tests
    - `composer unit-tests`
* Run WordPress Integration Tests
    - `composer wp-tests`
* Fix All Code Formatting
    - `composer formatting`
    
    
## WordPress and Caldera Forms Dependency
For now, this library is dependent on Caldera Forms and WordPress (for `\WPDB`.) This will change, possibly with breaking changes, when [caldera-interop](https://github.com/CalderaWP/caldera-interop) is integrated with this tool.

## Stuff.
Copyright 2018 CalderaWP LLC. License: GPL v2 or later.
