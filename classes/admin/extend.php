<?php

/**
 * Manage extend sub-menu
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Admin_Extend {


	/**
	 * Enqueue scripts for the admin extend sub menu
	 *
	 * @uses "admin_enqueue_scripts" action
	 *
	 * @since 1.4.2
	 */
	public static function scripts(){
		wp_enqueue_script( Caldera_Forms::PLUGIN_SLUG . '-handlebars', CFCORE_URL . 'assets/js/handlebars.js', array( 'jquery' )  );
		wp_enqueue_style( Caldera_Forms::PLUGIN_SLUG  . '-admin-styles', CFCORE_URL . 'assets/css/admin.css', array(), Caldera_Forms::VERSION );
	}
}