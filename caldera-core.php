<?php
/*
  Plugin Name: Caldera Forms
  Plugin URI: https://CalderaForms.com
  Description: Easy to use, grid based responsive form builder for creating simple to complex forms.
  Author: Caldera Labs
  Version: 1.5.2.1
  Author URI: http://CalderaLabs.org
  Text Domain: caldera-forms
  GitHub Plugin URI: https://github.com/CalderaWP/Caldera-Forms/
  GitHub Branch:     master
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CFCORE_PATH', plugin_dir_path(__FILE__));
define('CFCORE_URL', plugin_dir_url(__FILE__));
define( 'CFCORE_VER', '1.5.2.1' );
define('CFCORE_EXTEND_URL', 'https://api.calderaforms.com/1.0/');
define('CFCORE_BASENAME', plugin_basename( __FILE__ ));

/**
 * Caldera Forms DB version
 *
 * @since 1.3.4
 *
 * PLEASE keep this an integer
 */
define( 'CF_DB', 5 );

// init internals of CF
include_once CFCORE_PATH . 'classes/core.php';

add_action( 'init', array( 'Caldera_Forms', 'init_cf_internal' ) );
// table builder
register_activation_hook( __FILE__, array( 'Caldera_Forms', 'activate_caldera_forms' ) );


// load system
add_action( 'plugins_loaded', 'caldera_forms_load', 0 );
function caldera_forms_load(){

	include_once CFCORE_PATH . 'classes/autoloader.php';
	include_once CFCORE_PATH . 'classes/widget.php';
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_DB', CFCORE_PATH . 'classes/db' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Entry', CFCORE_PATH . 'classes/entry' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Email', CFCORE_PATH . 'classes/email' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Admin', CFCORE_PATH . 'classes/admin' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Render', CFCORE_PATH . 'classes/render' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Sync', CFCORE_PATH . 'classes/sync' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_CSV', CFCORE_PATH . 'classes/csv' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Processor_Interface', CFCORE_PATH . 'processors/classes/interfaces' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_API', CFCORE_PATH . 'classes/api' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Field', CFCORE_PATH . 'classes/field' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Magic', CFCORE_PATH . 'classes/magic' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Processor', CFCORE_PATH . 'processors/classes' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Shortcode', CFCORE_PATH . 'classes/shortcode' );

	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms', CFCORE_PATH . 'classes' );
	Caldera_Forms_Autoloader::register();

	// includes
	include_once CFCORE_PATH . 'includes/ajax.php';
	include_once CFCORE_PATH . 'includes/field_processors.php';
	include_once CFCORE_PATH . 'includes/custom_field_class.php';
	include_once CFCORE_PATH . 'includes/filter_addon_plugins.php';
	include_once CFCORE_PATH . 'includes/compat.php';
	include_once CFCORE_PATH . 'processors/functions.php';
	include_once CFCORE_PATH . 'includes/functions.php';

	/**
	 * Runs after all of the includes and autoload setup is done in Caldera Forms core
	 *
	 * @since 1.3.5.3
	 */
	do_action( 'caldera_forms_includes_complete' );

}

add_action( 'plugins_loaded', array( 'Caldera_Forms', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'Caldera_Forms_Tracking', 'get_instance' ) );


// Admin & Admin Ajax stuff.
if ( is_admin() || defined( 'DOING_AJAX' ) ) {
	add_action( 'plugins_loaded', array( 'Caldera_Forms_Admin', 'get_instance' ) );
	add_action( 'plugins_loaded', array( 'Caldera_Forms_Support', 'get_instance' ) );
	include_once CFCORE_PATH . 'includes/plugin-page-banner.php';
}
