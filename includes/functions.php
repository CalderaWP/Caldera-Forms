<?php

/**
 * Filter input and return sanitized output
 *
 * Wrapper for Caldera_Forms_Sanitize::sanitize()
 *
 * @param mixed $input The string, array, or object to sanitize
 * @param array $params Additional options
 *
 * @return array|mixed|object|string|void
 *
 * @since 1.4.0
 *
 */
function caldera_forms_sanitize( $input, $params = array() ){
	return Caldera_Forms_Sanitize::sanitize( $input, $params  );
}

/**
 * Clean the crap out of a string.
 *
 * Applies  stripslashes_deep, strip_tags and trim. This is a function of industry, often too much industry.
 *
 * @since 1.4.0
 *
 * @param $string
 *
 * @return string
 */
function caldera_forms_very_safe_string( $string ){
	return trim( strip_tags( stripslashes_deep( $string ) ) );
}


add_action( 'init', 'caldera_forms_fix_license_manger_link' );

/**
 * Redirect old license page link to new license page link
 *
 * @since 1.4.4
 */
function caldera_forms_fix_license_manger_link(){
	if( ! is_admin() ){
		return;
	}
	global $pagenow;
	if( 'options-general.php' == $pagenow && isset( $_GET[ 'page' ] ) && 'calderawp_license_manager' == $_GET[ 'page' ]  ){
		cf_redirect( add_query_arg( 'page', 'calderawp_license_manager', self_admin_url( 'wp-admin/admin.php') ) );
	}
}

add_filter('nonce_user_logged_out', 'caldera_forms_woo_nonce_fix', 100, 2 );

/**
 * If WooCommerce changes logged out nonce user ID, change it back to zero when checking Caldera Forms nonce.
 *
 * Workaround for https://github.com/CalderaWP/Caldera-Forms/issues/894
 *
 * @param int $user_id User ID
 * @param string $action Nonce action
 *
 * @return int
 */
function caldera_forms_woo_nonce_fix( $user_id, $action) {
	if ( class_exists( 'WooCommerce' ) ) {
		if ( $user_id && $user_id != 0 && $action && $action == 'caldera_forms_front' ) {
			$user_id = 0;
		}

	}

	return $user_id;

}
