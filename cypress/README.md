
## Start The Runner
Cypress opens a small app called "Cypress" and Chrome. You start this with the command `npm run test:e2e`. This is separate app then the Chrome you normally use, which [is confusing](https://docs.cypress.io/guides/guides/launching-browsers.html#Browser-Icon).


In the Cypress app, you should se a list of tests, you can click one to launch it in Chrome or you can click the "Run All Specs" button to run all of the tests.

## Runner Not Working?
* Cypress gets 404s for all pages?
    - Did you start the development environment? `composer wp:start`
    - Are you trying to use your own URL? You need to modify cypress.json's env, but then you will break it for everyone, please use the provided environment.

## Adding Tests