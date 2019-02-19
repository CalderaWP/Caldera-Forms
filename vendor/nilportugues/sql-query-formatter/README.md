SQL Query Formatter
=================

[![Build Status](https://travis-ci.org/nilportugues/sql-query-formatter.svg)](https://travis-ci.org/nilportugues/sql-query-formatter) [![Coverage Status](https://img.shields.io/coveralls/nilportugues/sql-query-formatter.svg)](https://coveralls.io/r/nilportugues/sql-query-formatter?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nilportugues/sql-query-formatter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nilportugues/sql-query-formatter/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/a57aa8f3-bbe1-43a5-941e-689d8435ab20/mini.png)](https://insight.sensiolabs.com/projects/a57aa8f3-bbe1-43a5-941e-689d8435ab20) [![Latest Stable Version](https://poser.pugx.org/nilportugues/sql-query-formatter/v/stable)](https://packagist.org/packages/nilportugues/sql-query-formatter) [![Total Downloads](https://poser.pugx.org/nilportugues/sql-query-formatter/downloads)](https://packagist.org/packages/nilportugues/sql-query-formatter) [![License](https://poser.pugx.org/nilportugues/sql-query-formatter/license)](https://packagist.org/packages/nilportugues/sql-query-formatter) 


A very lightweight PHP class that re-formats unreadable or computer-generated SQL query statements to human-friendly readable text.

* [1.Installation](#block1)
* [2. Features](#block2)
* [3. Usage](#block3)
* [4. Code Quality](#block5)
* [5. Author](#block6)
* [6. Special Thanks](#block6)
* [7. License](#block7)

<a name="block1"></a>
## 1.Installation
The recommended way to install the SQL Query Formatter is through [Composer](http://getcomposer.org). Run the following command to install it:

```sh
php composer.phar require nilportugues/sql-query-formatter
```

<a name="block2"></a>
## 2. Features

**Human readable SQL formatting**

- Human readable plain text. No colours, no highlighting. Plain text is good enough in most cases.

**Data Binding Awareness**

- SQL Query Formatter takes data binding seriously.
- Placeholder syntax such as `:variable` or `?` is taken into account and is preserved when formatting.


<a name="block3"></a>
## 3. Usage

Sample code:
```php
<?php
use NilPortugues\Sql\QueryFormatter\Formatter;

$query = <<<SQL
SELECT user.user_id, user.username, (SELECT 
role.role_name FROM role WHERE (role.role_id = :v1) 
LIMIT :v2, :v3 ) AS user_role, (SELECT 
role.role_name FROM role WHERE (role.role_id = :v4)
LIMIT :v5, :v6 ) AS role FROM user WHERE (user.user_id = :v7)
SQL;

$formatter = new Formatter();
echo $formatter->format($query);
```

Real output:
```sql
SELECT
    user.user_id,
    user.username,
    (
        SELECT
            role.role_name
        FROM
            role
        WHERE
            (role.role_id = :v1)
        LIMIT
            :v2,
            :v3
    ) AS user_role,
    (
        SELECT
            role.role_name
        FROM
            role
        WHERE
            (role.role_id = :v4)
        LIMIT
            :v5,
            :v6
    ) AS role
FROM
    user
WHERE
    (user.user_id = :v7)

```

<a name="block4"></a>
## 4. Fully tested
Testing has been done using PHPUnit and [Travis-CI](https://travis-ci.org). All code has been tested to be compatible from PHP 5.4 up to PHP 5.6 and [HHVM (nightly release)](http://hhvm.com/).

To run the test suite, you need [Composer](http://getcomposer.org):

```bash
    php composer.phar install --dev
    bin/phpunit
```


<a name="block5"></a>
## 5. Author
Nil Portugués Calderó

 - <contact@nilportugues.com>
 - [http://nilportugues.com](http://nilportugues.com)


<a name="block6"></a>
## 6. Special Thanks
I would like to thank the following people:

- [Jeremy Dorn](mailto:jeremy@jeremydorn.com) for his [sql-formatter](https://github.com/jdorn/sql-formatter) implementation I used as a basis for building this version.


<a name="block7"></a>
## 7. License
SQL Query Formatter is licensed under the MIT license.

```
Copyright (c) 2015 Nil Portugués Calderó

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```
