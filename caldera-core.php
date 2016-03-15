<?php
/*
  Plugin Name: Caldera Forms
  Plugin URI: https://calderawp.com/caldera-forms/
  Description: Easy to use, grid based responsive form builder for creating simple to complex forms.
  Author: David Cramer
  Version: 1.3.4-b1
  Author URI: https://calderawp.com
  Text Domain: caldera-forms
  GitHub Plugin URI: https://github.com/Desertsnowman/Caldera-Forms/
  GitHub Branch:     current-stable
 */

//initilize plugin

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CFCORE_PATH', plugin_dir_path(__FILE__));
define('CFCORE_URL', plugin_dir_url(__FILE__));
define('CFCORE_VER', '1.3.4-b1');
define('CFCORE_EXTEND_URL', 'https://api.calderaforms.com/1.0/');

/**
 * Caldera Forms DB version
 *
 * @since 1.3.4
 *
 * PLEASE keep this an integer
 */
define( 'CF_DB', 2 );

include_once CFCORE_PATH . 'classes/core.php';
include_once CFCORE_PATH . 'classes/widget.php';
include_once CFCORE_PATH . 'classes/sanitize.php';
include_once CFCORE_PATH . 'classes/forms.php';

// includes
include_once CFCORE_PATH . 'includes/ajax.php';
include_once CFCORE_PATH . 'includes/field_processors.php';
include_once CFCORE_PATH . 'includes/custom_field_class.php';
include_once CFCORE_PATH . 'includes/filter_addon_plugins.php';
include_once CFCORE_PATH . 'processors/classes/load.php';
include_once CFCORE_PATH . 'processors/classes/get_data.php';

// init internals of CF urls
add_action( 'init', array( 'Caldera_Forms', 'init_cf_internal' ) );
// table builder
register_activation_hook( __FILE__, array( 'Caldera_Forms', 'activate_caldera_forms' ) );

add_action( 'plugins_loaded', array( 'Caldera_Forms', 'get_instance' ) );

// Admin & Admin Ajax stuff.
if ( is_admin() || defined( 'DOING_AJAX' ) ) {

	require_once( CFCORE_PATH . 'classes/admin.php' );
	add_action( 'plugins_loaded', array( 'Caldera_Forms_Admin', 'get_instance' ) );

}

if ( is_admin() ) {
  require_once( CFCORE_PATH . 'processors/classes/ui.php' );
}

add_action( 'admin_init', 'caldera_forms_db_update' );
function caldera_forms_db_update(){

	if( current_user_can( 'manage_options' )  ){
		$db_version = get_option( 'CF_DB', 0 );
		if( CF_DB > $db_version ) {
			include_once CFCORE_PATH . 'includes/updater.php';
			if (  $db_version < 2   ) {
				caldera_forms_db_v2_update();
			}
		}


	}
}


