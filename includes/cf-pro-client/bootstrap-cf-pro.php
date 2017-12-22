<?php
use \calderawp\calderaforms\pro\container;
use \calderawp\calderaforms\pro\admin\scripts;
use \calderawp\calderaforms\pro\admin\menu;
/**
 * Load up Caldera Forms Pro API client
 *
 * @since 0.0.1
 */
add_action( 'caldera_forms_includes_complete', function(){
	$db_ver_option = 'cf_pro_db_v';
	//add database table if needed
	if( 1 > get_option( $db_ver_option, 0 ) ){
		caldera_forms_pro_drop_tables();
		caldera_forms_pro_db_delta_1();
		//set to 2 to skip autoload disable on new installs
		update_option( $db_ver_option, 2 );
	}

	if( 2 > get_option( $db_ver_option, 0 ) ){
		caldera_forms_pro_db_delta_2();
		update_option( $db_ver_option, 2 );
	}

	include_once __DIR__ .'/vendor/autoload.php';

	//add menu page
	if ( is_admin() ) {
		$slug       = 'cf-pro';
		$assets_url = plugin_dir_url( __FILE__  ) . 'dist/';
		$view_dir =  __DIR__ . '/dist';
		$scripts = new scripts( $assets_url, $slug, CF_PRO_VER );
		if( Caldera_Forms_Admin::is_edit() ){
			add_action( 'admin_init', function() use ( $scripts, $view_dir ){
                $tab = new \calderawp\calderaforms\pro\admin\tab( __DIR__ . '/dist/tab.php' );
				add_action( 'caldera_forms_get_panel_extensions', [ $tab, 'add_tab' ] );
				container::get_instance()->set_tab_html( $scripts->webpack( $view_dir, 'tab', false ) );

			} );

		}

		$menu = new menu( $view_dir, $slug, $scripts);
		add_action( 'admin_menu', [ $menu, 'display' ] );
	}

	//add hooks
	container::get_instance()->get_hooks()->add_hooks();

	/**
	 * Runs after Caldera Forms Pro is loaded
	 *
	 * @since 0.5.0
	 */
	do_action( 'caldera_forms_pro_loaded' );

});


/**
 * Delete CF Pro DB Table
 */
function caldera_forms_pro_drop_tables(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf_pro_messages';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);
	delete_option('cf_pro_db_v');
}

/**
 * Database changes for Caldera Forms Pro
 *
 * @since 0.0.1
 */
function caldera_forms_pro_db_delta_1(){
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	}

	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE $wpdb->collate";
	}
	$table = "CREATE TABLE `" . $wpdb->prefix . "cf_pro_messages` (
			`ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`cfp_id` bigint(20) unsigned DEFAULT NULL,
			`entry_id` bigint(20) unsigned DEFAULT NULL,
			`hash` varchar(255) DEFAULT NULL,
			`type` varchar(255) DEFAULT NULL,
			PRIMARY KEY ( `ID` )
			) " . $charset_collate . ";";

	dbDelta( $table );


}


/**
 * Rewrite the options to not autoload
 *
 * @since 0.8.1
 */
function caldera_forms_pro_db_delta_2(){

	$forms = Caldera_Forms_Forms::get_forms(false);
	if( ! empty( $forms ) ){
		array_walk( $forms, function( $id ){
			return '_cf_pro_' . caldera_forms_very_safe_string( $id );
		});
		//set options storage to be not autoloaded
		$where = '`option_name` = "' . implode( '" OR `option_name` = "', array_keys( $forms ) ) . '"';

		global $wpdb;
		$sql = sprintf( "UPDATE `%s` SET `autoload`='no' WHERE %s", $wpdb->options, $where );
		$wpdb->get_results( $sql  );

	}

}

/**
 * Get the URL for the Caldera Forms Pro App
 *
 * @since 0.0.1
 *
 * @return string
 */
function caldera_forms_pro_app_url(){

	if( ! defined( 'CF_PRO_APP_URL' ) ){
		/**
		 * Default URL for CF Pro App
		 */
		define( 'CF_PRO_APP_URL', 'https://app.calderaformspro.com' );

	}

	/**
	 * Filter URL for Caldera Forms Pro app
	 *
	 * Useful for local dev or running your own instance of app
	 *
	 * @since 0.0.1
	 *
	 * @param string $url The root URL for app
	 */
	return untrailingslashit( apply_filters( 'caldera_forms_pro_app_url', CF_PRO_APP_URL ) );
}


/**
 * Get the URL for the Caldera Forms Pro log app
 *
 * @since 0.8.0
 *
 * @return string
 */
function caldera_forms_pro_log_url(){

	/**
	 * Filter URL for Caldera Forms Pro log app
	 *
	 * Useful for local dev or running your own instance of app
	 *
	 * @since 0.8.0
	 *
	 * @param string $url The root URL for app
	 */
	return untrailingslashit( apply_filters( 'caldera_forms_pro_log_url', 'https://logger.calderaformspro.com' ) );

}



/**
 * Create HTML for linl
 *
 * @param array $form Form config
 * @param string $link The actual link.
 *
 * @return string
 */
function caldera_forms_pro_link_html( $form, $link ){

	/**
	 * Filter the classes for the generate PDF link HTML
	 *
	 * @param string $classes The classes as string.
	 * @param array $form Form config
	 */
	$classes = apply_filters( 'caldera_forms_pro_link_classes', ' alert alert-success', $form );


	/**
	 * Filter the visible content for the generate PDF link HTML
	 *
	 * @param string $message Link message
	 * @param array $form Form config
	 */
	$message = apply_filters( 'caldera_forms_pro_link_message', __( 'Download Form Entry As PDF', 'caldera-forms', $form ), $form );

	/**
	 * Filter the title attribute for the generate PDF link HTML
	 *
	 * @param string $title Title attribute.
	 * @param array $form Form config
	 */
	$title = apply_filters( 'caldera_forms_pro_link_title',  __( 'Download Form Entry As PDF', 'caldera-forms' ), $form );

	return sprintf( '<div class="%s"><a href="%s" title="%s" target="_blank">%s</a></div>',
		esc_attr( $classes ),
		esc_url( $link ),
		esc_attr( $title ),
		esc_html( $message )
	);
}


if( ! function_exists( 'caldera_forms_safe_explode' ) ){
    /**
     * Safely exploded, what might be a string with a comma
     * @since 1.5.8
     *
     * @param $string
     * @return array
     */
	function caldera_forms_safe_explode( $string ){
		if( false === strpos( $string, ',' ) ){
			return array( $string );
		}
		return explode(',', $string );
	}
}

/**
 * Compare public key and token to saved keys
 *
 * @since 1.5.8
 * @since 0.9.0
 *
 * @param string $public Public key to check
 * @param string $token Token to check
 *
 * @return bool
 */
function caldera_forms_pro_compare_to_saved_keys( $public, $token ){
	$settings = container::get_instance()->get_settings();
	return hash_equals( $public, $settings->get_api_keys()->get_public() ) && hash_equals( $settings->get_api_keys()->get_token(), $token );
}

/**
 * Create the URL for file request endpoints
 *
 * @since 1.5.8
 * @since 0.9.0
 *
 * @param string $path File path
 *
 * @return string
 */
function caldera_forms_pro_file_request_url( $path ){
	return add_query_arg( 'file', urlencode( $path ), Caldera_Forms_API_Util::url( 'pro/file' ) );
}

/**
 * Shim for boolval in PHP v5.5
 *
 * @since 0.3.1
 */
if ( ! function_exists( 'boolval' ) ) {
	function boolval( $val ){
		return (bool) $val;

	}

}

/**
 * Activation hook callback
 *
 * @since 0.11.0
 */
function caldera_forms_pro_activation_hook_callback(){
	//make sure we have autoloader
	include_once __DIR__ .'/vendor/autoload.php';

	//delete old message tracking transient keys -- should only be one
	$past_versions = get_option(  'cf_pro_past_versions', [] );
	if( ! empty( $past_versions ) ){
		foreach ( $past_versions as $i => $version ){
			Caldera_Forms_Transient::delete_transient( caldera_forms_pro_log_tracker_key( $version ) );
			unset( $past_versions[$i] );
		}

	}
	$past_versions[] = CF_PRO_VER;

	update_option( 'cf_pro_past_versions', $past_versions, 'no'  );

}

/**
 * Get the name of the CF transient (not actual transient) used to track repeat log messages
 *
 * @since 0.11.0
 *
 * @param string $version Version number
 *
 * @return string
 */
function caldera_forms_pro_log_tracker_key( $version ){
	return md5( __FUNCTION__ . $version );
}

