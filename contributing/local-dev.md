# Local WordPress Test Environment
All PHP tests are based off of the WordPress "unit" test suite, and therefore need a full WordPress test environment. The install script in '/bin' is pretty standard and should work with VVV or whatever.

We provide a docker-based development environment. It is recommended that you use this environment because the setup is scripted and all of the tests can be run with it.

The local server is [http://localhost:8228](http://localhost:8228)


#### Requirements
* Docker
    - [Installation documentation](https://docs.docker.com/install/)
* Composer
    - [Installation documentation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
* npm
    - [Installation documentation](https://www.npmjs.com/get-npm)
    
    
#### Install Test Environment
* Install local development environment, dependencies and setup test forms
    - `composer dev:install`
        -  May take awhile.
* Go to [http://localhost:8228](http://localhost:8228) and make sure you have a WordPress site and can login.
    - Username: admin
    - password: password
* [See Commands](https://github.com/CalderaWP/Caldera-Forms/tree/develop#composer)
