<?php
/**
 * Utility functions for use when rendering form
 *
 * Will be placed in .cf-fieldjs-config for each field
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Render_Util {

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

}