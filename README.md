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

This creates `/build/<version>` where `<version>` is the current version set in `package.json`.
### JavaScript Development

Run watcher for development to auto-compile JavaScript and CSS

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

#### Commands
* `composer wp-install` - Installs Docker-based test environment.
* `composer wp-tests` - Runs phpunit inside of Docker container.
* `composer wp-stop` - Stops Docker-based test environment, without destroying containers.
* `composer wp-remove` - Stops Docker-based test environment and destroys containers.


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
