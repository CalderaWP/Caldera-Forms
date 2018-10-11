<?php
// phpcs:disable
/**
 * This is the bootstrap file for Unit Tests -- run using composer unit-tests
 */

//Autoloader
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';


/** Translation compatibility */
if (! function_exists('translate')) {
	/**
	 * @param string $text
	 * @return string mixed
	 */
	function translate($text)
	{
		return $text;
	}
}
if (! function_exists('__')) {
	/**
	 * @param string $text   Text to translate.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 * @return string Translated text.
	 */
	function __($text, $domain = 'default')
	{
		return translate($text, $domain);
	}
}


//WordPress WPDB constants
if ( ! defined( 'ARRAY_A')) {
	define('OBJECT', 'OBJECT');
	define('object', 'OBJECT');
	define('OBJECT_K', 'OBJECT_K');
	define('ARRAY_A', 'ARRAY_A');
	define('ARRAY_N', 'ARRAY_N');
}