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
		Caldera_Forms_Render_Assets::register();
		Caldera_Forms_Render_Assets::enqueue_script( 'handlebars' );
		Caldera_Forms_Admin_Assets::enqueue_style( 'admin' );
	}
}