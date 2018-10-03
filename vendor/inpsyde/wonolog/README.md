# Wonolog 

[![Version](https://img.shields.io/packagist/v/inpsyde/wonolog.svg)](https://packagist.org/packages/inpsyde/wonolog)
[![Status](https://img.shields.io/badge/status-active-brightgreen.svg)](https://github.com/inpsyde/Wonolog)
[![Build](https://img.shields.io/travis/inpsyde/Wonolog.svg)](http://travis-ci.org/inpsyde/Wonolog)
[![Downloads](https://img.shields.io/packagist/dt/inpsyde/wonolog.svg)](https://packagist.org/packages/inpsyde/wonolog)
[![License](https://img.shields.io/packagist/l/inpsyde/wonolog.svg)](https://packagist.org/packages/inpsyde/wonolog)

![Wonolog](assets/images/banner.png)

> Monolog-based logging package for WordPress.

------

## Table of Contents

- [Introduction](#introduction)
- [Minimum Requirements and Dependencies](#minimum-requirements-and-dependencies)
- [Getting Started](#getting-started)
- [Wonolog Defaults](#wonolog-defaults)
- [Learn More](#learn-more)
- [License and Copyright](#license-and-copyright)

------

## Introduction

Wonolog is a Composer package (not a plugin) that allows to log anything that *happens* in a WordPress site.

It is based on [Monolog](https://github.com/Seldaek/monolog), which, with its over 38 millions of downloads and thousands of dependent packages, is the most popular logging library for PHP, compatible with the PSR-3 standard.


## Minimum Requirements and Dependencies

Wonolog requires:

- PHP 5.6+
- WordPress 4.6+

Via [Composer](https://getcomposer.org), Wonolog requires `monolog/monolog` (MIT).

When installed for development, via Composer, Wonolog also requires:

- `phpunit/phpunit` (BSD-3-Clause)
- `brain/monkey` (MIT)
- `mikey179/vfsStream` (BSD-3-Clause)


## Getting Started

Wonolog should be installed via Composer.
Its package name is `inpsyde/wonolog`.

**The suggested way to use Wonolog is at website level.**

If you don't use Composer to manage your whole website then Wonolog is probably not for you.
You might be able to use it anyway, but support is not guaranteed.

It's easily possible to develop plugins and themes compatible with Wonolog logging even without explicitly declaring it as a dependency.

A couple of noteworthy things:

- all Wonolog configurations have to be done in a MU plugin;
- in a WordPress mutlisite installation, all Wonolog configurations are _naturally_ site-wide.

On the bright side, Wonolog comes with a super easy bootstrap routine and some out-of-the-box configurations that make it possible to have a working and effective logging system with zero effort.

To get started with defaults settings, this is required:

1. install Wonolog via Composer;
1. ensure Composer autoload is loaded in `wp-config.php` or anytime before the `'muplugins_loaded'` action is fired;
1. create a **MU plugin** that, at least, contains this code:

```php
<?php
Inpsyde\Wonolog\bootstrap();
```


## Wonolog Defaults

The three steps described above are all that is necessary to have a working logging system that uses Monolog to write logs in a file.
The path of that file changes based on current date, using the following format:

- `{WP_CONTENT_DIR}/wonolog/{Y/m/d}.log`,

with `{Y/m/d}` being replaced by `date( 'Y/m/d' )`.

For example, a target file could be `/wp-content/2017/02/27.log`.

What is actually logged depends on the value of `WP_DEBUG_LOG` constant.

When `WP_DEBUG_LOG` is set to `true`, Wonolog will log *everything*.
When `WP_DEBUG_LOG` is set to `false`, Wonolog will only log events with a log level higher or equal to `ERROR`, according to [PSR-3 log levels](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#5-psrlogloglevel).

"Automatically" logged events include:

- PHP core notices, warnings and (fatal) errors;
- uncaught exceptions;
- WordPress errors and events (e.g., DB errors, HTTP API errors, `wp_mail()` errors, and 404 errors).

**This is just the default behavior.**

The `bootstrap()` function provides entry points for many configurations and customizations.

Moreover, the package provides both action and filter hooks, and can be configured via environment variables, which makes Wonolog **very** flexible, and exposes all the power that Monolog provides.


## Learn More

Documentation of Wonolog features, defaults, configuration and ways to extends it can be found in separate files:

- [01 - Monolog Primer](docs/01-monolog-primer.md) to learn a bit more about Monolog core concepts.
- [02 - Basic Wonolog Concepts](docs/02-basic-wonolog-concepts.md) to learn the basics of logging with Wonolog.
- [03 - A Deeper Look at Wonolog](docs/03-a-deeper-look-at-wonolog.md) to learn more advanced concepts and features of Wonolog.
- [04 - Hook Listeners](docs/04-hook-listeners.md) to read about *hook listeners*, the powerful feature of Wonolog that allows for logging any WordPress code.
- [05 - Wonolog Customization](docs/05-wonolog-customization.md) for a deep travel through all the possible configurations available for any aspect of the package.
- [06 - Custom Hook Listeners](docs/06-custom-hook-listeners.md) to see a complete example of a custom hook listener, its integration in Wonolog, and all the things that you need to know in order to write reusable Wonolog extensions.


## License and Copyright

Copyright (c) 2017 Inpsyde GmbH.

Wonolog code is licensed under [MIT license](https://opensource.org/licenses/MIT).

The team at [Inpsyde](https://inpsyde.com) is engineering the Web since 2006.
