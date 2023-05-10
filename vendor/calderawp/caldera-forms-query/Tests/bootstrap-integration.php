<?php
// phpcs:disable
/**
 * This is the bootstrap file for Integration Tests -- run with composer wp-tests
 */

$_tests_dir = getenv('WP_TESTS_DIR');
if (! $_tests_dir) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the LIBRARY being tested and plugins it depends on
 */
function _manually_load_plugin()
{
	//Include autoloader
	require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
	//Add Caldera Forms
	require __DIR__ . '/plugins/caldera-forms/caldera-core.php';
	//Add some Caldera Forms testing tools
	require_once  __DIR__ .'/plugins/caldera-forms/tests/includes/traits/has-mock-form.php';
	require_once  __DIR__ .'/plugins/caldera-forms/tests/includes/traits/has-data.php';
	require_once  __DIR__ .'/plugins/caldera-forms/tests/includes/traits/imports-form.php';
	require_once  __DIR__ .'/plugins/caldera-forms/tests/includes/traits/submits-contact-form.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
// phpcs:enable
