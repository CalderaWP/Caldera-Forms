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
     * @param array $params {
     *     Optional. Additional options
     *
     *     @type string $nested
     *     @type string $type Sprintf type to use %s|%d|%f
     *     @type bool   $strip_scripts If true, all scripts will be removed from output. Default is false.
     *     @type bool $strip_tags If true, all tags are stripped. Default is false.
     * }
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
            'type' => null, // %s %d %f etc
            'strip_tags' => false,
            'strip_scripts' => false,
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

        if ( true === $params[ 'strip_scripts' ] ){
            $output = self::remove_scripts( $output );
        }

        if( true === $params[ 'strip_tags' ] ){
            $output = strip_tags( $output );
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

	/**
	 * Strip all scripts -- not just tags -- from a string
	 *
	 * Loosely based on wp_strip_all_tags()
	 *
	 * @since 1.5.7
	 *
	 * @param string $string   String containing HTML tags
	 * @return string The processed string.
	 */
	public static function remove_scripts($string ) {
	    if( is_array( $string ) ){
            if (! empty( $string)) {
                foreach ($string as $i => $part) {
                    $string[$i] = self::remove_scripts($part);
                }
            }
            return $string;
        }
		return str_replace( 'javascript:', '', preg_replace( '@<(script|iframe)[^>]*?>.*?</\\1>@si', '', $string ));

	}

    /**
     * Remove unintentional line breaks in a string, such as an email header
     *
     * @since 1.5.9
     *
     * @param string $header
     * @return string
     */
	public static function sanitize_header($header){
        return preg_replace( '=((<CR>|<LF>|0x0A/%0A|0x0D/%0D|\\n|\\r)\S).*=i', null, $header );

	}
	
	/**
     * Remove partially removed line breaks (backslash removed but not r or n)
     *
     * @since 1.8.6
     *
     * @param string $string
     * @return string
     */
	public static function finish_trim($string){
		return rtrim( $string, " \\r\\n" );
    }

}


