<?php

/**
 * Handles settings for Caldera Forms emails
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Email_Settings {

	/**
	 * Name of options key to store settings
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	protected static $option_key = '_caldera_forms_email_api_settings';

	/**
	 * Current settings
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected static $settings;

	/**
	 * Nonce action for settings UI
	 * 
	 * @since 1.4.0
	 * 
	 * @var string
	 */
	protected static $nonce_action = 'cf-emails';

	/**
	 * Default settings
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected static $defaults = array(
		'sendgrid' => array(
			'key' => false
		),
		'wp' => array(
		
		),
		'method' => 'wp'
	);

	/**
	 * Get settings
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	public static function get_settings() {
		if ( null == self::$settings ) {
			self::$settings = get_option( self::$option_key, array() );
			self::$settings = wp_parse_args( self::$settings, self::$defaults );
		}

		return self::$settings;
	}

	/**
	 * Get API keys
	 *
	 * @since 1.4.0
	 *
	 * @param string $api Which API to get key for
	 *
	 * @return mixed
	 */
	public static function get_key( $api ) {
		if( self::is_allowed_method( $api ) ){
			return self::$settings[ $api ]['key'];
		}

	}

	/**
	 * Save an API key
	 *
	 * @since 1.4.0
	 *
	 * @param string $api API to save key for
	 * @param string $key API Key
	 * @param bool $save Optional. If true, the default, settings will be saved.
	 */
	public static function save_key( $api, $key, $save = true ) {
		if ( self::is_allowed_method( $api ) ) {
			if( ! is_array( self::$settings[ $api ] ) ){
				self::$settings[ $api ] = array( 'key' => false );
			}

			self::$settings[ $api ][ 'key' ] = $key;
			if ( $save ) {
				self::update_settings();
			}


		}

	}

	/**
	 * Check if is an allowed API
	 * 
	 * @since 1.4.0
	 * 
	 * @param string $api Name of API
	 *
	 * @return bool
	 */
	public static function is_allowed_method( $api ){
		return  in_array( $api, self::allowed_apis() );

	}

	/**
	 * Save email settings
	 * 
	 * @uses "wp_ajax_cf_email_save" action
	 * 
	 * @since 1.3.5
	 */
	public static function save(){
		if( ! current_user_can( Caldera_Forms::get_manage_cap( 'admin' ) ) ) {
			wp_die();
		}
		
		if( isset( $_POST[ 'nonce' ] ) && wp_verify_nonce( $_POST[ 'nonce' ], self::$nonce_action ) ){
			if( isset( $_POST[ 'method' ] ) ){
				if ( self::is_allowed_method( $_POST[ 'method' ] ) ) {
					self::$settings[ 'method' ] = $_POST[ 'method' ];
				}
			}

			foreach( self::allowed_apis() as $api ){
				if( isset( $_POST[ $api ] ) && is_string( $_POST[ $api ] ) ){
					self::save_key( $api, trim( strip_tags( $_POST[ $api ] )  ), false );
				}
			}

			self::update_settings();
			status_header( 200);
			wp_send_json_success();


		}

		wp_send_json_error();
		
	}


	/**
	 * If possible add hook to use set API
	 *
	 * @uses "init" action
	 *
	 * @since 1.4.0
	 */
	public static function maybe_add_hooks() {
		//don't load in PHP 5.2
		if ( ! version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
			return;
		}

		self::get_settings();

		if( 'wp' !== self::get_method() ){
			foreach ( self::allowed_apis() as $api ) {
				if ( self::valid( $api ) ) {
					add_filter( 'caldera_forms_mailer', array( 'Caldera_Forms_Email_Callbacks', $api ), 26, 3 );
					break;
				}

			}
			
		}


	}

	/**
	 * Create nonce field for settings
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public static function nonce_field(){
		return wp_nonce_field( self::$nonce_action, 'cfemail', false, false );
	}


	/**
	 * Is API valid?
	 *
	 * @since 1.4.0
	 *
	 * @param string $api API name
	 
	 * @return bool
	 */
	protected static function valid( $api ) {
		self::get_settings();
		if ( self::is_allowed_method( $api ) ) {
			if ( isset( self::$settings[ $api ]['key'] ) && ! empty( self::$settings[ $api ]['key'] ) ) {
				return true;

			}

		}

	}

	/**
	 * Get an array of valid APIs
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	protected static function allowed_apis() {
		/**
		 * Filter allowed email APIs
		 *
		 * @since 1.4.0
		 *
		 * @param array $apis Array of API names that are valid.
		 */
		return apply_filters( 'caldera_forms_allowed_email_apis', array(
			'wp',
			'sendgrid',
			'caldera'
		) );
	}

	/**
	 * Create email settings UI
	 *
	 * @uses "caldera_forms_admin_footer" action
	 *
	 * @since 1.4.0
	 */
	public static function ui() {
		if ( ! version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
			printf( '<div class="notice notice-error error"><p>%s</p>', esc_html__( 'Switching email services requires PHP 5.4 or later. PHP 5.6 is strongly recommended.', 'caldera-forms' ) );
		}else{
			include CFCORE_PATH . '/ui/emails/settings.php';

		}
		


	}

	/**
	 * Get current method being used
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public static function get_method(){
		if( ! isset( self::$settings[ 'method' ] ) ){
			return 'wp';

		}

		return self::$settings[ 'method' ];
	}

	/**
	 * Update email settings
	 *
	 * @since 1.4.0
	 */
	protected static function update_settings() {
		update_option( self::$option_key, self::$settings );
	}

	/**
	 * Sanitize/validate save of this setting
	 *
	 * @since 1.4.0
	 *
	 * @uses "pre_update_option__caldera_forms_email_api_settings"
	 *
	 * @param mixed $values Values to be saved
	 *
	 * @return array
	 */
	public static function sanitize_save( $values ){
		if( ! is_array( $values ) ){
			return self::$defaults;
		}
		
		foreach ( $values as $key => $value ){
			if( ! array_key_exists( $key, self::$defaults ) ){
				unset( $values[ $key ] );
			}elseif( 'method' == $key ){
				if( ! self::is_allowed_method( $value ) ){
					$values[ 'method' ] = 'wp';
				}
			}else{
				foreach( $value as $k => $v ){
					if( ! in_array( $k, array( 'key', 'use' ) ) ){
						unset( $values[ $key ][ $k ] );
					}
				}
			}
		}

		foreach ( self::allowed_apis() as $api ){
			if( ! isset( $values[ $api ] ) ){
				$values[ $api ] = self::$defaults[ $api ];
			}
		}
		
		
		return $values;
		
	}

}
