Caldera-Forms
=============
<a href="https://calderaforms.com/"><img src="https://calderaforms.com/wp-content/uploads/2015/02/catdeta-caldera-forms-banner.png" /></a>
Drag & Drop WordPress Responsive Form Builder


## Docs, Add-ons & More Information:
* [Getting Started](https://calderaforms.com/getting-started)
* [Documentation](https://calderaforms.com/documentation/caldera-forms-documentation/)
* [Caldera Forms Pro](https://calderaforms.com/pro)

## Development
[Contributor Guidleines](https://github.com/CalderaWP/Caldera-Forms/blob/master/CONTRIBUTING.md)


### Install for development
Requires: git, npm, Grunt.
- Clone repo to plugin directory
    - `git clone git@github.com:CalderaWP/Caldera-Forms.git`
- Switch directory
    - `cd Caldera-Forms.git`
- npm install
    - `npm i`
### Build For Release
To create a build to ship to WordPress.org:
`npm run package`

This creates `/build/<version>` where `<version>` is the current version set in `package.json`. This creates a directory you can ZIP up for testing or whatever.

See "Release To WordPress.org" section below for more details on pushing this build to WordPress.org. 

### JavaScript Development

Run watcher for development to auto-compile JavaScript and CSS.

#### `/clients/`
This is the new stuff, built with webpack. Eventually everything UI will be here.

* Clients:
    * pro - The Caldera Forms Pro admin page and tab in the editor.
    * blocks - The Gutenberg block(s).
    * admin - The main admin page. Work in progress, not used in plugin.
    * viewer - The entry viewer. Work not in progress, not used in plugin.
    * editor - Theoretical.

* Build for development and start watcher.
    - `npm run dev`
* Build for production
    -  `npm run build:webpack`
    
#### `/assets/`
This is the old stuff, built with grunt.


* Build for development and start watcher.
    - `npm run dev:grunt`
* Build for production
    -  `npm run build:grunt`
    
### Test Environment
All PHP tests are based off of the WordPress "unit" test suite, and therefore need a full WordPress test environment. The install script in '/bin' is pretty standard and should work with VVV or whatever.

Alternatively, because this, isn't 2014, you can use the provided Docker environment.
#### Requirements
* Docker
    - [Installation documentation](https://docs.docker.com/install/)
* Composer
    - [Installation documentation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
* npm
    - [Installation documentation](https://www.npmjs.com/get-npm)
    
### Test Structures
* PHP tests go in /tests and are run using phpunit
* JavaScript tests go in clients/tests
- Unit tests go in clients/tests/unit and are run using [Jest](https://facebook.github.io/jest/docs/en/getting-started.html)
- Unit tests must have the word test in file name. For example, `formConfig.test.js`

#### Commands
* `composer wp-install` - Installs Docker-based test environment.
* `composer wp-start` - Starts Docker-based test environment.
* `composer wp-tests` - Runs phpunit inside of Docker container.
* `composer wp-stop` - Stops Docker-based test environment, without destroying containers.
* `composer wp-remove` - Stops Docker-based test environment and destroys containers.
* `npm test` - Run JavaScript test watcher
* `npm run test:once` - Run JavaScript unit tests once

### Release To WordPress.org
##### Requires
* [svn](https://subversion.apache.org/quick-start#installing-the-client)
* [npm](https://www.npmjs.com/get-npm)
* Grunt `npm install -g grunt-cli`
* [Have commit access to Caldera Forms svn](https://wordpress.org/plugins/caldera-forms/advanced/#committer-list)

#### Steps
* Build release file
    - `npm package`
* Push Tag to WordPress.org
    - `cd bin`
    - `bash deploy-wp-org-tag.sh 12345 christiechirinos`
* Install tag using WP Rollback on QA site and re-run Ghost Inspector tests.
* Copy tag to trunk
    - `bash deploy-wp-org-trunk.sh 12345 christiechirinos`

#### Notes
* This assumes your WordPress.org username is `christiechirinos`, and your password is `12345`.
* The first argument is password, which is required. The second argument is username, which defaults to `Shelob9`, which is Josh's username.

## Contributing/ Using This Repo, Etc.
* The default branch is "master" that should be the same as WordPress.org.
* Development happens on the "develop" branch. [There may be an exception, see: https://github.com/CalderaWP/Caldera-Forms/blob/master/CONTRIBUTING.md#current-git-workflow--php-compatibility](https://github.com/CalderaWP/Caldera-Forms/blob/master/CONTRIBUTING.md#current-git-workflow--php-compatibility)
* If you find a bug, or would like to make a feature request, [please open an issue](https://github.com/CalderaWP/Caldera-Forms/issues/).
* If you fixed a bug, or made a new feature, please submit a pull request against the develop branch.


## Contributing/ Using This Repo, Etc.
* The default branch is "master" that should be the same as WordPress.org.
* Development happens on the "develop" branch.
* If you find a bug, or would like to make a feature request, [please open an issue](https://github.com/CalderaWP/Caldera-Forms/issues/).
* If you fixed a bug, or made a new feature, please submit a pull request against the develop branch.
