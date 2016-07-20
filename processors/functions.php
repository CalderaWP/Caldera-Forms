<?php

/**
 * Get User IP
 *
 * Returns the IP address of the current visitor
 *
 * @since 1.3.5.3
 *
 * @return string $ip User's IP address
 */
function caldera_forms_get_ip() {

	$ip = '127.0.0.1';

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;

}

/**
 * Get current  URL

 * @since 1.3.5.3
 *
 * @return string Current URL
 */
function caldera_forms_get_current_url(){
	$url = 'http';

	if ( isset( $_SERVER[ 'HTTPS' ] ) && 'off' != $_SERVER[ 'HTTPS' ] && 0 != $_SERVER[ 'HTTPS' ] )
		$url = 'https';

	$url .= '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];

	return $url;

}
