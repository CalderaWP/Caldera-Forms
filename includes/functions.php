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


/**
 * Create (with escaping) a field attributes string for a Caldera Forms field input
 *
 * @since 1.5.0
 *
 * @param array $attrs Array of attributes, $name => $value
 * @param array $field Field config
 * @param array $form Form config
 *
 *
 * @return  string
 */
function caldera_forms_field_attributes( array $attrs, array $field, array $form ){
	$field_type = Caldera_Forms_Field_Util::get_type( $field, $form );

	/**
	 * Filter field attributes before rendering
	 *
	 * @since 1.5.0
	 *
	 * @param array $attrs Array of attributes, $name => $value
	 * @param array $field Field config
	 * @param array $form Form config
	 */
	$attrs = apply_filters( 'caldera_forms_field_attributes', $attrs, $field, $form );

	/**
	 * Filter field attributes before rendering for a specific field type
	 *
	 * @since 1.5.0
	 *
	 * @param array $attrs Array of attributes, $name => $value
	 * @param array $field Field config
	 * @param array $form Form config
	 */
	$attrs = apply_filters( "caldera_forms_field_attributes-$field_type", $attrs, $form );

	return caldera_forms_implode_field_attributes( caldera_forms_escape_field_attributes_array( $attrs ) );



}

/**
 *  Escape an array of HTML attributes
 *
 * @since 1.5.0
 *
 * @param array $attrs Array of attributes, $name => $value
 *
 * @return array
 */
function caldera_forms_escape_field_attributes_array( array  $attrs, $prefix = null ){
	$out = array();
	foreach ( $attrs as $attr => $value  ) {
		if( $prefix ){
			$attr = $prefix . $attr;
		}

		if( is_array( $value ) ){
			$_value = '';
			foreach ( $value as $v ){
				$_value .= ' ' . esc_attr( $v );
			}
			$out[ $attr ] = $_value;
		}else{
			$out[ $attr ] = esc_attr( $value );
		}
	}

	return $out;
}

/**
 * Implode an escaped array of field attributes
 *
 * @since 1.5.0
 *
 * @param array $attrs
 *
 * @return string
 */
function caldera_forms_implode_field_attributes( array $attrs ){
	$out = '';
	$pattern = '%s="%s" ';
	foreach (  $attrs as $attr => $value  ) {
		if ( 0 != $value  ) {
			if ( empty( $value ) ) {

			}
		}

		$out .= sprintf( $pattern, $attr, $value );
	}

	return $out;
}

/**
 * Get all editable WordPress roles
 *
 * @since 1.5.0
 *
 * @return array
 */
function caldera_forms_get_roles(){
	global $wp_roles;
	$all_roles      = $wp_roles->roles;
	return  apply_filters( 'editable_roles', $all_roles );

}

/**
 * Returns the current release series
 *
 * For 1.5.x will return 5 -- when we get to 2, should return 20, 21, etc.
 *
 * @since 1.5.0
 *
 * @return string
 */
function caldera_forms_get_release_series(){
	return substr( CFCORE_VER, 2, 1 );
}

/**
 * Shim for boolval added in PHP v5.5
 *
 * Developer note: Don't use boolval() use (bool) $value but this is here for saftey's sake.
 * @See: https://github.com/CalderaWP/Caldera-Forms/issues/1727
 *
 * @since 1.5.2.1
 */
if ( ! function_exists( 'boolval' ) ) {
	function boolval( $val ){
		return (bool) $val;
	}

}
