Starting in 2018, Caldera Forms began to adopt test-driven development (TDD). We are as test-driven as we can and employ [several layers of testing](https://twitter.com/kentcdodds/status/960723172591992832/photo/1?ref_src=twsrc%5Etfw%7Ctwcamp%5Etweetembed%7Ctwterm%5E960723172591992832&ref_url=https%3A%2F%2Fblog.kentcdodds.com%2Fmedia%2F8417946d2d96f984d9eec36afc3ed94f%3FpostId%3D5e8c7fff591c) -

* Unit tests 
  - Isolated tests run with Jest (JavaScript) or phpunit (PHP).
* Integration tests
  - We use the WordPress "unit" test suite for these tests. This repo provides a [Docker environment](https://github.com/CalderaWP/Caldera-Forms/tree/develop#test-environment) that they run it.
* Acceptance tests
  - We use Cypress and Ghost Inspector.
  

## JavaScript Testing

### Location Of Tests
https://github.com/CalderaWP/Caldera-Forms/tree/develop#test-structures

### Tools

* [Jest](https://jest.io) - Unit tests, assertions and test runner.
* [react-test-renderer](https://reactjs.org/docs/test-renderer.html) Basic React rendering for unit tests. Run by Jest.
* [Enzyme]() - More advanced React rendendering for isolated DOM testing and other intergration tests. Run by Jest.
* [Cypress]() - Acceptance tests run against a real WordPress site.

### Recommended Reading

I wrote a series of posts on testing React in context of Gutenberg for Torque:

* [Getting Started](https://torquemag.io/2018/10/getting-started-with-react-unit-testing-for-wordpress-development/)
* [Jest](https://torquemag.io/2018/11/testing-nested-components-in-a-react-app/)
* [Enzyme]()
* [jQuery and other legacy concerns](https://torquemag.io/2019/01/testing-jquery-with-jest-in-wordpress-development/)

Other recommended reading:
* [Write Tests, Not Too Many](https://blog.kentcdodds.com/write-tests-not-too-many-mostly-integration-5e8c7fff591c)
* [Cypress](https://deliciousbrains.com/cypress-testing/)

## PHP Testing

### Location Of Tests
https://github.com/CalderaWP/Caldera-Forms/tree/develop#test-structures

### Tools

### Recommended Reading
