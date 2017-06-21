<?php
/**
 * Utility functions for use when rendering form
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
		if( null === $current_form_count  ){
			$current_form_count = 0;
		}

		return absint( $current_form_count );
	}

	/**
	 * Get ID attribute for a form
	 *
	 * @since 1.5.8
	 *
	 * @param int $current_form_count Current form count on page
	 *
	 * @return string
	 */
	public static function form_id_attr( $current_form_count ){
		//JOSH - Don't put a filter here SO MANY things assume this is the way it is
		$form_wrap_id = "caldera_form_" . $current_form_count;
		return $form_wrap_id;
	}

	/**
	 * Get ID attribute for a form
	 *
	 * @since 1.5.0
	 * @deprecated 1.5.0.8
	 *
	 * @param int $current_form_count Current form count on page
	 *
	 * @return string
	 */
	public static function field_id_attribute( $current_form_count ){
		//Deprecated beacuse naming was wrong
		//See: https://github.com/CalderaWP/Caldera-Forms/issues/1489
		_deprecated_function( 'Caldera_Forms_Render_Util::field_id_attribute', 'Caldera_Forms_Render_Util::form_id_attr', '1.5.0.8');
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

	/**
	 * Add an inline script to footer scripts
	 *
	 * @since 1.5.0.8
	 *
	 * @param string $script JavaScript with not <script> tags
	 * @param array $form Form config
	 *
	 * @return bool
	 */
	public static function add_inline_script( $script, array  $form ){
		$script = self::create_inline_script( $script );

		return self::add_inline_data( $script, $form );
	}

	/**
	 * Add CData markup to footer scripts
	 *
	 * @since 1.5.0.8
	 *
	 * @param $script
	 * @param array $form
	 *
	 * @return bool
	 */
	public static function add_cdata( $script, array $form ){
		$output = self::create_cdata( $script );
		return self::add_inline_data(  $output, $form );

	}

	/**
	 * Create inline script markup
	 *
	 * @since 1.5.0.8
	 *
	 * @param string $script JavaScript with not <script> tags
	 *
	 * @return string
	 */
	protected static function create_inline_script( $script ){
		$script = sprintf( "<script type='text/javascript'>\n%s\n</script>\n", $script );

		return $script;
	}

	/**
	 * Create CData markup
	 *
	 * @since 1.5.0.8
	 *
	 * @param string $script JavaScript with not <script> tags
	 *
	 * @return string
	 */
	public static function create_cdata( $script ){
		$output = "<script type='text/javascript'>\n"; // CDATA and type='text/javascript' is not needed for HTML 5
		$output .= "/* <![CDATA[ */\n";
		$output .= "$script\n";
		$output .= "/* ]]> */\n";
		$output .= "</script>\n";

		return $output;
	}


}
