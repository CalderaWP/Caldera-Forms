<?php
/*
  Plugin Name: Caldera Forms
  Plugin URI: http://digilab.co.za
  Description: Create simple to complex grid based, responsive forms quickly and easily.
  Author: David Cramer
  Version: 1.0.3
  Author URI: http://digilab.co.za
 */

//initilize plugin

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CFCORE_PATH', plugin_dir_path(__FILE__));
define('CFCORE_URL', plugin_dir_url(__FILE__));
define('CFCORE_VER', '1.0.3');
define('CFCORE_EXTEND_URL', 'http://digilab.co.za');

// table builder
register_activation_hook( __FILE__, array( 'Caldera_Forms_Admin', 'activate_caldera_forms' ) );

include_once CFCORE_PATH . 'classes/core.php';
include_once CFCORE_PATH . 'classes/widget.php';

add_action( 'plugins_loaded', array( 'Caldera_Forms', 'get_instance' ) );

// Admin & Admin Ajax stuff.
if ( is_admin() || defined( 'DOING_AJAX' ) ) {

	require_once( CFCORE_PATH . 'classes/admin.php' );
	add_action( 'plugins_loaded', array( 'Caldera_Forms_Admin', 'get_instance' ) );

}

