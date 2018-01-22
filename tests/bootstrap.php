<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Caldera_Forms
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/caldera-core.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

include_once( dirname( dirname( __FILE__ ) ) . '/includes/updater.php' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

//include test forms
include_once( dirname( __FILE__ ) . '/includes/forms/contact-form-include.php' );
include_once( dirname( __FILE__ ) . '/includes/forms/simple-form-with-just-a-text-field-include.php' );

create_testing_db_tables();

//include test case
include_once( dirname( __FILE__ ) . '/includes/cf-test-case.php' );
include_once( dirname( __FILE__ ) . '/includes/cf-rest-test-case.php' );

/**
 * Create a fake DB table
 */
function create_testing_db_tables(){
    global $wpdb;
    $charset_collate = '';

    if ( ! empty( $wpdb->charset ) ) {
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    }

    if ( ! empty( $wpdb->collate ) ) {
        $charset_collate .= " COLLATE $wpdb->collate";
    }


    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $table =  $wpdb->prefix . "cf_db_abstraction_test";
    $wpdb->query("DROP TABLE IF EXISTS $table");

    $tacking_table = "CREATE TABLE `" . $wpdb->prefix . "cf_db_abstraction_test` (
			`ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`a_id` varchar(255) DEFAULT NULL,
			`b_id` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`ID`)
			) " . $charset_collate . ";";

    dbDelta( $tacking_table );


    $table =  $wpdb->prefix . "cf_db_abstraction_test_meta";
    $wpdb->query("DROP TABLE IF EXISTS $table");

    $meta_table = "CREATE TABLE `" . $wpdb->prefix . "cf_db_abstraction_test_meta` (
			`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`a_id` bigint(20) unsigned NOT NULL DEFAULT '0',
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext,
			PRIMARY KEY (`meta_id`),
			KEY `meta_key` (`meta_key`),
			KEY `a_id` (`a_id`)
			) " . $charset_collate . ";";

    dbDelta( $meta_table );



}
