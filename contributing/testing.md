# Automated Testing For Caldera Forms

Starting in 2018, Caldera Forms began to adopt test-driven development (TDD). I [wrote a post about adopting TDD based on this](https://torquemag.io/2019/01/adopting-tdd-for-an-existing-plugin/)
 We are as test-driven as we can and employ [several layers of testing](https://twitter.com/kentcdodds/status/960723172591992832/photo/1?ref_src=twsrc%5Etfw%7Ctwcamp%5Etweetembed%7Ctwterm%5E960723172591992832&ref_url=https%3A%2F%2Fblog.kentcdodds.com%2Fmedia%2F8417946d2d96f984d9eec36afc3ed94f%3FpostId%3D5e8c7fff591c) -

* Unit tests 
  - Isolated tests run with Jest (JavaScript) or phpunit (PHP).
* Integration tests
  - We use the WordPress "unit" test suite for these tests. This repo provides a [Docker environment](https://github.com/CalderaWP/Caldera-Forms/tree/develop#test-environment) that they run it.
* Acceptance tests
  - We use Cypress and Ghost Inspector.

## Installation
All of these tools are installed and run with Composer and npm. [See local dev documentation](./local-dev.md)

## Test-First
NOTE: You should read the article on [pull request workflow](./pull-request-workflow.md) before developing for Caldera Forms.

When working on a bug or feature, you should follow [red/green TDD flow](https://www.codecademy.com/articles/tdd-red-green-refactor):
* Write tests that would pass if the bug was fixed or the feature was completed.
* Push to Github and open a pull request 
    * Automated tests should fail.
    * Travis and Github will indicate PR can not be merged with a RED light.
* Write code that makes the tests pass.
* Push to Github
    * Automated tests should pass
    * Travis and Github will indicate PR can not be merged with a GREEN light.

### Recommended Reading
* [Using TDD For WordPress Plugin Development](https://torquemag.io/2018/08/advanced-oop-for-wordpress-part-8-developing-new-features-for-extensible-wordpress-plugins-using-test-driven-development/)
## Locations For Tests 
* PHP tests go in /tests and are run using phpunit
    - Unit tests -- isolated tests that do NOT require WordPress -- go in `tests/Unit`.
        * These tests are run with `composer test:unit`. 
        * You may NOT use any functions from WordPress core, without mocking.
    - Integration tests, which require WordPress, are in `/tests` and `/tests/Integration`
        * Tests in `tests/Integration` are newer tests, and use a PSR-4 autoloader, etc.
        * Tests in the main `/tests` are older. That used to be all of our tests.
            - For the most part, only add integration tests in `tests/Integration`
    - The trait `calderawp\calderaforms\Util\Traits` should have all of the factories used for integration and unit tests.
        * This is an aspirational goal, and partially true.
* JavaScript UNIT tests go in clients/tests
    - Unit tests go in clients/tests/unit and are run using [Jest](https://facebook.github.io/jest/docs/en/getting-started.html)
    - Unit tests must have the word test in file name. For example, `formConfig.test.js`
* End to end tests go in `cypress/integration` amd are written using [Cypress](https://cypress.io)
    - See our [Cypress README for testing](./cypress/README.md)

## JavaScript Testing

We write our tests using [Jest](https://jest.io). 
### Tools

* [Jest](https://jest.io) - Unit tests, assertions and test runner.
* [react-test-renderer](https://reactjs.org/docs/test-renderer.html) Basic React rendering for unit tests. Run by Jest.
* [Enzyme]() - More advanced React rendendering for isolated DOM testing and other integration tests. Run by Jest.

### Recommended Reading

I wrote a series of posts on testing React in context of Gutenberg for Torque:

* [Getting Started](https://torquemag.io/2018/10/getting-started-with-react-unit-testing-for-wordpress-development/)
* [Jest](https://torquemag.io/2018/11/testing-nested-components-in-a-react-app/)
* [Enzyme]()
* [jQuery and other legacy concerns](https://torquemag.io/2019/01/testing-jquery-with-jest-in-wordpress-development/)

Other recommended reading:
* [Write Tests, Not Too Many](https://blog.kentcdodds.com/write-tests-not-too-many-mostly-integration-5e8c7fff591c)

### Relevant CLI Commands
* `npm test` - Run JavaScript test watcher
* `npm run test:once` - Run JavaScript unit tests once
* `composer test:setup` - Adds test forms and puts them on pages.

## PHP Testing
We use phpunit to run unit tests and acceptance tests.

### Tools
* [phpunit](https://phpunit.de/)

### Relevant CLI Commands
* `composer test:php` - Run PHP tests -- isolated unit tests and the WordPress integration tests.
* `composer wp:tests` - Runs the PHP integration tests using phpunit inside Docker-based environment.
* `composer test:unit` - Run php unit tests.

### Recommended Reading

* [Intro To WordPress "unit" Testing]https://torquemag.io/2017/07/practical-guide-unit-testing-code/
* [Unit Testing WordPress Plugins](https://torquemag.io/2018/04/advanced-oop-wordpress-part-3-unit-testing-wordpress-rest-api-plugins/)
* [Using The WordPress Test Suite](https://torquemag.io/2018/05/advanced-oop-for-wordpress-part-5-using-the-wordpress-test-suite-for-integration-testing/

## Acceptance Testing
We primarily use Ghost Inspector to write and run acceptance tests against a live WordPress site. We have not yet automated this part. For Caldera Forms 1.8, we added acceptance tests with Cypress, because they are written in code and easier to automate. Also, we can run them locally.

### Relevant CLI Commands
* `npm run test:e2e` - Start Cypress e2e test runner.

### Tools Used
* [Cypress](https://cypress.io) - Acceptance tests run against the [local development environment](./local-dev.md).
* [Ghost Inspector](https://ghostinspector.com)

### Recommenced Reading
* [Cypress](https://deliciousbrains.com/cypress-testing/)
