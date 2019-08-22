# PHP Coding Standards

## Introduction
This document answers the question of "what PHP coding standards to follow when using Caldera Forms?" 

## Historical Background

Before Caldera Forms 1.5.? all classes were located in `/classes` and followed a very ad-hoc standard. The Caldera Forms Pro client was merged into Caldera Forms 1.5.? and uses PSR-4 with snake case naming, but otherwise is correct. Caldera Forms 1.8.0 introduced the `/cf2` directory. These are classes for Caldera Forms 2.0. The goal is/ was for 2.0 or 3.0 to drop the `/classes` directory, leaving us with modern, well tested code. Until we get there, this history lesson and variable answer is needed.

### Important Notes
* A linter is [not yet](https://github.com/CalderaWP/Caldera-Forms/issues/2207) in use.
* Caldera Forms supports PHP 5.6 or later. Please use new language features when they makes sense. Square brackets for arrays is prefered. Annonymous functions, late static bindings, etc. can be used when they make sense.

## Inline Documentation
Inline documentation is required for all filters, actions, methods and functions. Documentation should conform to the [WordPress PHP inline documentation standards](https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/)

Important annotations:
  * `@since` Make sure to include a version number to help add-on developers
  * `@covers` In phpunit tests, make sure to reference what method is covered.
  
  
## Tabs vs Spaces
Use tabs.

## PHP Classes
## `/classes`
Classes inside of this directory are autloaded using the [Caldera Forms Autoloader](https://calderaforms.com/doc/using-caldera-forms-autoloader/). Therefore, if a class is in the root of `/classes` and is in a file fancy.php, then it would need to be named `Caldera_Forms_Fancy`. If it is in the directory `/classes/fancy` and in a file called pants.php, it must be called `Caldera_Forms_Fancy_Pants`.

Inside of these classes, other standards are the WordPress PHP coding standards: snake case variable and method naming, spacing around parens.

## `/includes/cf-pro-client/classes`
Classes are autoloaded using a almost standard PSR-4 autoloader. The root namespace is `calderawp\calderaforms\pro` and file naming and class naming is PSR-4, but snake-cased. Therefore if you added a class `ninja_forms` to the directory `/includes/cf-pro-client/classes` the file name would be `ninja_forms.php`. You can also add new namespaces, so a class called `ninja_forms` in the namespace `calderawp\calderaforms\pro\plugins` would be located in `/includes/cf-pro-client/classes/plugins` and be called `ninja_forms.php`.

## `/cf2`

This directory uses a totally standard PSR-4 autoloader. All methods, class names and variables should be snakeCased and otherwise follow PSR-1/2. A lint [following these standards would be applicable](https://github.com/CalderaWP/caldera/blob/master/php-packages/rest-api/phpcs.xml.dist)

## PHP Functions

PHP fucntions are prefixed with `caldera_forms_` and should follow WordPress coding standards.

PHP functions go in `/includes` or `cf2/functions.php`.

