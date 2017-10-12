<?php
/**
 * Compatibility functions for functionality from PHP extensions or not present in old PHP versions
 *
 * @package   Caldera_Froms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

/**
 * Wrapper for "http_build_url" -- will use http_build_url() if it exists, if not will do same thing.
 *
 * See: http://php.net/manual/en/function.http-build-query.php
 *
 * @param $url
 * @param array $parts
 * @param int $flags
 * @param bool|false $new_url
 *
 * @return string
 */
function cf_http_build_url( $url, $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = false ) {
	if ( function_exists( 'http_build_url' ) ) {
		return http_build_url( $url, $parts, $flags, $new_url );
	}

	$keys = array( 'user', 'pass', 'port', 'path', 'query', 'fragment' );

	if ( $flags & HTTP_URL_STRIP_ALL ) {
		$flags |= HTTP_URL_STRIP_USER;
		$flags |= HTTP_URL_STRIP_PASS;
		$flags |= HTTP_URL_STRIP_PORT;
		$flags |= HTTP_URL_STRIP_PATH;
		$flags |= HTTP_URL_STRIP_QUERY;
		$flags |= HTTP_URL_STRIP_FRAGMENT;
	} elseif ( $flags & HTTP_URL_STRIP_AUTH ) {
		$flags |= HTTP_URL_STRIP_USER;
		$flags |= HTTP_URL_STRIP_PASS;
	}

	// Parse the original URL
	$parse_url = parse_url( $url );

	// Scheme and Host are always replaced
	if ( isset( $parts[ 'scheme' ] ) ) {
		$parse_url[ 'scheme' ] = $parts[ 'scheme' ];
	}
	if ( isset( $parts[ 'host' ] ) ) {
		$parse_url[ 'host' ] = $parts[ 'host' ];
	}


	if ( $flags & HTTP_URL_REPLACE ) {
		foreach ( $keys as $key ) {
			if ( isset( $parts[ $key ] ) ) {
				$parse_url[ $key ] = $parts[ $key ];
			}
		}
	} else {

		if ( isset( $parts[ 'path' ] ) && ( $flags & HTTP_URL_JOIN_PATH ) ) {
			if ( isset( $parse_url[ 'path' ] ) ) {
				$parse_url[ 'path' ] = rtrim( str_replace( basename( $parse_url[ 'path' ] ), '', $parse_url[ 'path' ] ), '/' ) . '/' . ltrim( $parts[ 'path' ], '/' );
			} else {
				$parse_url[ 'path' ] = $parts[ 'path' ];
			}
		}


		if ( isset( $parts[ 'query' ] ) && ( $flags & HTTP_URL_JOIN_QUERY ) ) {
			if ( isset( $parse_url[ 'query' ] ) ) {
				$parse_url[ 'query' ] .= '&' . $parts[ 'query' ];
			} else {
				$parse_url[ 'query' ] = $parts[ 'query' ];
			}
		}
	}


	foreach ( $keys as $key ) {
		if ( $flags & (int) constant( 'HTTP_URL_STRIP_' . strtoupper( $key ) ) ) {
			unset( $parse_url[ $key ] );
		}

		if( isset( $parse_url[ $key ]   ) && is_array( $parse_url[ $key ] ) ){
			$parse_url[ $key ] = http_build_query( $parse_url[ $key ] );
		}

	}


	$new_url = $parse_url;

	return ( ( isset( $parse_url[ 'scheme' ] ) ) ? $parse_url[ 'scheme' ] . '://' : '' )
	       . ( ( isset( $parse_url[ 'user' ] ) ) ? $parse_url[ 'user' ] . ( ( isset( $parse_url[ 'pass' ] ) ) ? ':' . $parse_url[ 'pass' ] : '' ) . '@' : '' )
	       . ( ( isset( $parse_url[ 'host' ] ) ) ? $parse_url[ 'host' ] : '' )
	       . ( ( isset( $parse_url[ 'port' ] ) ) ? ':' . $parse_url[ 'port' ] : '' )
	       . ( ( isset( $parse_url[ 'path' ] ) ) ? $parse_url[ 'path' ] : '' )
	       . ( ( isset( $parse_url[ 'query' ] ) ) ? '?' . $parse_url[ 'query' ] : '' )
	       . ( ( isset( $parse_url[ 'fragment' ] ) ) ? '#' . $parse_url[ 'fragment' ] : '' );
}

if ( ! defined( 'HTTP_URL_REPLACE' ) ) {
	define( 'HTTP_URL_REPLACE', 1 );
}
if ( ! defined( 'HTTP_URL_JOIN_PATH' ) ) {
	define( 'HTTP_URL_JOIN_PATH', 2 );
}

if ( ! defined( 'HTTP_URL_JOIN_QUERY' ) ) {
	define( 'HTTP_URL_JOIN_QUERY', 4 );
}

if ( ! defined( 'HTTP_URL_STRIP_USER' ) ) {
	define( 'HTTP_URL_STRIP_USER', 8 );
}

if ( ! defined( 'HTTP_URL_STRIP_PASS' ) ) {
	define( 'HTTP_URL_STRIP_PASS', 16 );
}

if ( ! defined( 'HTTP_URL_STRIP_AUTH' ) ) {
	define( 'HTTP_URL_STRIP_AUTH', 32 );
}

if ( ! defined( 'HTTP_URL_STRIP_PORT' ) ) {
	define( 'HTTP_URL_STRIP_PORT', 64 );
}

if ( ! defined( 'HTTP_URL_STRIP_PATH' ) ) {
	define( 'HTTP_URL_STRIP_PATH', 128 );
}

if ( ! defined( 'HTTP_URL_STRIP_QUERY' ) ) {
	define( 'HTTP_URL_STRIP_QUERY', 256 );
}

if ( ! defined( 'HTTP_URL_STRIP_FRAGMENT' ) ) {
	define( 'HTTP_URL_STRIP_FRAGMENT', 512 );
}

if ( ! defined( 'HTTP_URL_STRIP_ALL' ) ) {
	define( 'HTTP_URL_STRIP_ALL', 1024 );
}
