<?php
/**
 * Caldera Forms Variable and Sanitize Class
 *
 * The code in this class is largely lifted from Pods (http://Pods.io), which is copyrighted by The Pods Foundation and license under the GPL. Muchas Gracias.
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 Josh Pollock
 */

/**
 * Caldera_Forms Caldera_Forms_Variables
 * @package Caldera_Forms
 * @author  Josh Pollock <Josh@JoshPress.net>
 */

class Caldera_Forms_Sanitize {


	/**
	 * Filter input and return sanitized output
	 *
	 * @param mixed $input The string, array, or object to sanitize
	 * @param array $params Additional options
	 *
	 * @return array|mixed|object|string|void
	 *
	 * @since 1.1.10
	 *
	 */
	public static function sanitize( $input, $params = array() ) {

		$input = stripslashes_deep( $input );

		if ( '' === $input || is_int( $input ) || is_float( $input ) || empty( $input ) ) {
			return $input;
		}

		$output = array();

		$defaults = array(
			'nested' => false,
			'type' => null // %s %d %f etc
		);

		if ( !is_array( $params ) ) {
			$defaults[ 'type' ] = $params;

			$params = $defaults;
		}
		else {
			$params = array_merge( $defaults, (array) $params );
		}

		if ( is_object( $input ) ) {
			$input = get_object_vars( $input );

			$n_params = $params;
			$n_params[ 'nested' ] = true;

			foreach ( $input as $key => $val ) {
				$output[ self::sanitize( $key ) ] = self::sanitize( $val, $n_params );
			}

			$output = (object) $output;
		}
		elseif ( is_array( $input ) ) {
			$n_params = $params;
			$n_params[ 'nested' ] = true;

			foreach ( $input as $key => $val ) {
				$output[ self::sanitize( $key ) ] = self::sanitize( $val, $n_params );
			}
		}
		elseif ( !empty( $params[ 'type' ] ) && false !== strpos( $params[ 'type' ], '%' ) ) {
			/**
			 * @var $wpdb wpdb
			 */
			global $wpdb;

			$output = $wpdb->prepare( $params[ 'type' ], $output );
		}
		else {
			$output = wp_slash( $input );
		}

		return $output;

	}

	/**
	 * Filter input and return sanitized SQL LIKE output
	 *
	 * @param mixed $input The string, array, or object to sanitize
	 *
	 * @return array|mixed|object|string|void
	 *
	 * @since 1.1.10
	 *
	 * @see like_escape
	 */
	public static function sanitize_like( $input ) {

		if ( '' === $input || is_int( $input ) || is_float( $input ) || empty( $input ) ) {
			return $input;
		}

		$output = array();

		if ( is_object( $input ) ) {
			$input = get_object_vars( $input );

			foreach ( $input as $key => $val ) {
				$output[ $key ] = self::sanitize_like( $val );
			}

			$output = (object) $output;
		}
		elseif ( is_array( $input ) ) {
			foreach ( $input as $key => $val ) {
				$output[ $key ] = self::sanitize_like( $val );
			}
		}
		else {
			global $wpdb;

			//backwords-compat check for pre WP4.0
			if ( method_exists( 'wpdb', 'esc_like' ) ) {
				$output = $wpdb->esc_like( self::sanitize( $input ) );
			}
			else {
				// like_escape is deprecated in WordPress 4.0
				$output = like_escape( self::sanitize( $input ) );
			}
		}

		return $output;

	}


	/**
	 * Filter input and return unslashed output
	 *
	 * @param mixed $input The string, array, or object to unsanitize
	 *
	 * @return array|mixed|object|string|void
	 *
	 * @since 1.1.10
	 *
	 * @see wp_unslash
	 */
	public static function unslash( $input ) {

		if ( '' === $input || is_int( $input ) || is_float( $input ) || empty( $input ) ) {
			return $input;
		}

		$output = array();

		if ( empty( $input ) ) {
			$output = $input;
		}
		elseif ( is_object( $input ) ) {
			$input = get_object_vars( $input );

			foreach ( $input as $key => $val ) {
				$output[ $key ] = self::unslash( $val );
			}

			$output = (object) $output;
		}
		elseif ( is_array( $input ) ) {
			foreach ( $input as $key => $val ) {
				$output[ $key ] = self::unslash( $val );
			}
		}
		else {
			$output = wp_unslash( $input );
			
		}

		return $output;

	}

	/**
	 * Remove an array of tags and their contents from a string
	 *
	 * @since 1.5.0
	 *
	 * @param string $html HTML to remove from
	 * @param array $tags The tags to remove, no < or > for example array( 'iframe', 'form' )
	 *
	 * @return string
	 */
	public static function remove_tags( $html, array $tags ){
		if( empty( $html ) || ! is_string( $html ) ){
			return '';
		}


		if (  ! empty( $tags ) ) {
			$doc = new DOMDocument();
			$doc->loadHTML( $html );
			$xpath = new DOMXPath( $doc );
			foreach ( $tags as $tag ) {
				foreach ( $xpath->query( '//' . $tag ) as $node ) {
					$node->parentNode->removeChild( $node );
				}
			}
			$html = $doc->textContent;
		}

		return $html;
	}
	
}


