<?php

/**
 * Creates entry viewer
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry_Viewer {

	/**
	 * Get full viewer system
	 *
	 * Designed for use in admin
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public static function full_viewer(){
		ob_start();
		include CFCORE_PATH . 'ui/entries/viewer.php';
		return ob_get_clean();
	}

	/**
	 * Print necessary scripts or admin viewer
	 *
	 * @since 1.5.0
	 */
	public static function print_scripts(){
		include CFCORE_PATH . 'ui/entries/navigation.php';
	}

}