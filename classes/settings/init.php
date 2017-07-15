<?php

/**
 * Initialize core settings
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Settings_Init {

	/**
	 * Load core settings objects
	 *
	 * Called in Caldera_Forms constructor
	 *
	 * @since 1.5.3
	 */
	public static function load(){
		//Call the Caldera_Forms::setings() method to trigger "caldera_forms_settings_registered" action
		add_action( 'caldera_forms_core_init', array( 'Caldera_Forms', 'settings' ) );
		add_action( 'caldera_forms_settings_registered', array( __CLASS__, 'add_core_settings' ) );
		if( ! is_admin() ){
			add_action( 'caldera_forms_settings_registered', array( 'Caldera_Forms_CDN_Init', 'init' ), 15 );
		}
	}

	/**
	 * Register the core settings
	 *
	 * CDN
	 * Email (@todo)
	 * General (@todo)
	 *
	 * @uses "caldera_forms_settings_registered" action
	 *
	 * @since 1.5.3
	 */
	public static function add_core_settings(){
		Caldera_Forms::settings()->add( new Caldera_Forms_CDN_Settings() );
	}


}