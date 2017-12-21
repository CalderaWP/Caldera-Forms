Changelog
=========

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
