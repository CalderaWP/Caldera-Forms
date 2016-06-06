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
	 * @since 1.3.6
	 *
	 * @var string
	 */
	protected static $option_key = '_caldera_forms_email_api_settings';

	/**
	 * Current settings
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected static $settings;

	/**
	 * Default settings
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected static $defaults = array(
		'sendgrid' => array(
			'use' => false,
			'key' => false
		)
	);

	/**
	 * Get settings
	 *
	 * @since 1.3.6
	 *
	 * @return array
	 */
	public static function get_settings(){
		if( null == self::$settings ){
			self::$settings = get_option( self::$option_key, array() );
			self::$settings = wp_parse_args( self::$settings, self::$defaults );
		}

		return self::$settings;
	}

	/**
	 * Get API keys
	 *
	 * @since 1.3.6
	 *
	 * @param string $api Which API to get key for
	 *
	 * @return mixed
	 */
	public static function get_key( $api ){
		if( self::valid( $api, false ) ){
			return self::$settings[ $api ][ 'key' ];
		}

	}

	/**
	 * Save an API key
	 *
	 * @since 1.3.6
	 *
	 * @param string $api API to save key for
	 * @param string $key API Key
	 * @param bool $use Optional. If true, the default, API will be enabled.
	 */
	public static function save_key( $api, $key, $use = true ){
		if( in_array( $api, self::allowed_apis()  ) ){
			self::$settings[ $api ][ $key ] = $key;
			if( $use ){
				self::$settings[ $api ][ 'use' ] = true;
			}

			update_option( self::$option_key, self::$settings );
		}

	}

	/**
	 * If possible add hook to use set API
	 *
	 * @uses "init" action
	 *
	 * @since 1.3.6
	 */
	public static function maybe_add_hooks(){
		//don't load in PHP 5.2
		if ( ! version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
			return;
		}
		self::get_settings();
		foreach ( self::$settings as $api => $args ) {
			if ( self::valid( $api, true ) ) {
				add_filter( 'caldera_forms_mailer', array( 'Caldera_Forms_Email_Callbacks', $api ), 26, 3 );
				break;
			}

		}

	}

	/**
	 * Is API valid?
	 *
	 * @since 1.3.6
	 *
	 * @param string $api API name
	 * @param bool $check_active Optional. If true, the default, check if is active and valid choice. If false, just check if is a valid choice.
	 *
	 * @return bool
	 */
	protected static function valid( $api, $check_active = true ){
		self::get_settings();
		if( in_array( $api, self::allowed_apis()  ) ) {
			if ( $check_active ) {
				if ( isset( self::$settings[ $api ][ 'use' ], self::$settings[ $api ][ 'key' ] ) && true == self::$settings[ $api ][ 'use' ] && ! empty( self::$settings[ $api ][ 'key' ] ) ) {
					return true;
				}

			}else{
				if ( isset( self::$settings[ $api ][ 'key' ] ) && ! empty( self::$settings[ $api ][ 'key' ] ) ) {
					return true;
				}
			}
		}
	}

	/**
	 * Get an array of valid APIs
	 *
	 * @since 1.3.6
	 *
	 * @return array
	 */
	protected static function allowed_apis(){
		/**
		 * Filter allowed email APIs
		 *
		 * @since 1.3.6
		 *
		 * @param array $apis Array of API names that are valid.
		 */
		return apply_filters( 'caldera_forms_allowed_email_apis', array(
			'sendgrid',
			'caldera'
		)  );
	}

}
