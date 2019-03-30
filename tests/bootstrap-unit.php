<?php
// phpcs:disable
/**
 * This is the bootstrap file for unit tests.
 */


$_plugin_root_dir = dirname(dirname(__FILE__ ));

// Load Patchwork before everything else in order to allow us to redefine WordPress and plugin functions.
require_once $_plugin_root_dir . '/vendor/brain/monkey/inc/patchwork-loader.php';
require_once $_plugin_root_dir . '/vendor/autoload.php';

/**
 * Interfaces from `/classes` needed in cf2
 */
require_once $_plugin_root_dir . '/classes/api/route.php';

if( ! defined( 'NONCE_SALT' ) ){
	define( 'NONCE_SALT', 'NONCE_SALTy' );
}
