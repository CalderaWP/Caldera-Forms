<?php
/**
 * Utility functions for use when rendering form
 *
 * Will be placed in added to DOM as CDATA
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Render_Util {

	public static $footer_objects;

	/**
	 * Get ID of form notice HTML element
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param int $count Form instance count
	 *
	 * @return string
	 */
	public static function notice_element_id( array  $form, $count ){
		$element = 'caldera_notices_' . $count;

		/**
		 * Filter ID of form notice HTML element
		 *
		 * @since 1.5.0
		 *
		 * @param string $element notice element ID
		 * @param array $form Form config
		 * @param int $count Form instance count
		 */
		return apply_filters( 'caldera_forms_render_notice_element_id', $element, $form, $count );

	}

	/**
	 * Get the current forms number
	 *
	 * If 1 form on page, will be 1, if 2 and is scodn form rendered will be 2...
	 * This is a wrapper for global $current_form_count in hopes that one day, that global will be removed
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	public static function get_current_form_count(){
		global $current_form_count;
		return absint( $current_form_count );
	}

	/**
	 * Get ID attribute for a form
	 *
	 * @since 1.5.0
	 *
	 * @param int $current_form_count Current form count on page
	 *
	 * @return string
	 */
	public static function field_id_attribute( $current_form_count ){
		//JOSH - Don't put a filter here SO MANY things assume this is the way it is
		$form_wrap_id = "caldera_form_" . $current_form_count;
		return $form_wrap_id;
	}

	/**
	 * Add data to be printed in footer
	 *
	 * Container/factory for Caldera_Forms_Render_Footer objects
	 *
	 * @uses 1.5.0
	 *
	 * @param string $data Data to add
	 * @param array $form Form config
	 *
	 * @return bool True if added, false if invalid or could not be added (not string or added too late)
	 */
	public static function add_inline_data( $data, array $form ){
		if( ! empty(  $form[ 'ID' ] ) ){
			$form_id =  $form[ 'ID' ];
		}else{
			return false;
		}

		if( empty( self::$footer_objects[ $form[ 'ID' ] ] ) ){
			if ( is_array( $form ) ) {
				self::$footer_objects[ $form_id ] = new Caldera_Forms_Render_Footer( $form );
			}
		}
		/** @var Caldera_Forms_Render_Footer */
		return self::$footer_objects[ $form_id ]->add_data( $data );

	}



}