<?php
/*
  Plugin Name: Caldera Forms
  Plugin URI: http://digilab.co.za
  Description: Form Building
  Author: David Cramer
  Version: 1.0.0
  Author URI: http://digilab.co.za
 */

//initilize plugin

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CFCORE_PATH', plugin_dir_path(__FILE__));
define('CFCORE_URL', plugin_dir_url(__FILE__));
define('CFCORE_VER', '1.0.0');

// table builder
register_activation_hook( __FILE__, array( 'Caldera_Forms', 'activate_caldera_forms' ) );

include_once CFCORE_PATH . 'classes/core.php';

Caldera_Forms::get_instance();