<?php

/**
 * Handles printing scripts for form in the footer
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_Render_Inline {

	/**
	 * Scripts to be printed
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected static $scripts;

	/**
	 * Print scripts in footer if we have them
	 *
	 * @since 1.5.0
	 */
	public static function print_scripts(){
		if( ! empty( self::$scripts ) ){
			foreach ( self::$scripts as $script ){
				echo $script;
			}

		}

	}

	/**
	 * Add a script to be printed in footer
	 *
	 * @since 1.5.0
	 *
	 * @param string $script
	 */
	public static function add_script( $script ){
		if( is_string( $script ) ){
			self::$scripts[] = $script;
			add_action( 'wp_footer', array( __CLASS__, 'print_scripts' ), 999 );
		}

	}

}