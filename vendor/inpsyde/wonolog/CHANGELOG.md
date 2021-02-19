Changelog
=========

## [v1.0.4 (2020-07-21)](https://github.com/inpsyde/Wonolog/releases/tag/1.0.4)

### Changed
- Do not log silenced errors by-default (See #50)

## [v1.0.3 (2019-09-11)](https://github.com/inpsyde/Wonolog/releases/tag/1.0.3)

### Changed
- Constrain major version of Monolog (See #44)

### Fixed
- Added support for default handler file path that contain spaces  (See #39)

## [v1.0.2 (2018-05-15)](https://github.com/inpsyde/Wonolog/releases/tag/1.0.2)

### Changed
- Raised severity of cron events to `INFO` (See #34)
- Update dev requirements in `composer.json`

### Fixed
- Broken tests after latest changes
- Log creation from `\WP_Error` without data (See #36)
- CS fix and other minor changes

## [v1.0.1 (2018-02-08)](https://github.com/inpsyde/Wonolog/releases/tag/1.0.1)

### Added

- CI tests for PHP 7.2

### Changed

- Improve generated `.htaccess` file to make it work with Apache 2.3+, see [#23](https://github.com/inpsyde/Wonolog/issues/23), props @chesio.
- Update some dev-dependencies (Brain Monkey) and adjust tests accordingly.

### Fixed

- Fix typo in documentation, see [#24](https://github.com/inpsyde/Wonolog/pull/24), props @chesio.
- Fix strict type issue when running tests in PHP 7.1+.
- Fix HTTP API Listener triggering a false-positive error for all non-blocking requests, see [#26](https://github.com/inpsyde/Wonolog/issues/26).

-------

## [v1.0.0 (2017-06-01)](https://github.com/inpsyde/Wonolog/releases/tag/1.0.0)

_First **public** release._

-------

## [v0.2.2 (2016-11-08)](https://github.com/inpsyde/Wonolog/releases/tag/0.2.2)

### Fixed

- Strict standard error in PHP 5.* that criticize re-declaration of properties in classes previously declared by trait, see [#4](https://github.com/inpsyde/Wonolog/issues/4)

-------

## [v0.2.1 (2016-10-25)](https://github.com/inpsyde/Wonolog/releases/tag/0.2.1)

### Fixed

- Bug with custom default handler be ignored

-------

## [v0.2.0 (2016-10-20)](https://github.com/inpsyde/Wonolog/releases/tag/0.2.0)

### Added

- Introduce support for `wonolog.log.{$level}` hooks
- Introduce new `MailerListener` to log `wp_mail` events

### Changed

- Changed `PhpErrorController` method names to use snake_case
- Refactoring of bootstrap file, delay of bootstrap routine to priority 20 of "muplugins_loaded"
- Updated README with info on new and changed features

-------

## [v0.1.1 (2016-10-20)](https://github.com/inpsyde/Wonolog/releases/tag/0.1.1)

### Fixed

- Removed type-hint from `PhpErrorController::onFatal()` because it causes issues with PHP7

-------

## [v0.1.0 (2016-10-18)](https://github.com/inpsyde/Wonolog/releases/tag/0.1.0)

_First release._
