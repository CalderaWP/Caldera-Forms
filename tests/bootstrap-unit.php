<?php
// phpcs:disable
/**
 * This is the bootstrap file for unit tests.
 */


<<<<<<< HEAD
$_plugin_root_dir = dirname(dirname(__FILE__ ));
=======
$_plugin_root_dir = dirname(dirname(__FILE__));

>>>>>>> develop
// Load Patchwork before everything else in order to allow us to redefine WordPress and plugin functions.
require_once $_plugin_root_dir . '/vendor/brain/monkey/inc/patchwork-loader.php';
require_once $_plugin_root_dir . '/vendor/autoload.php';
