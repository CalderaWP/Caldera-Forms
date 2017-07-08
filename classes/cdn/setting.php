<?php


/**
 * Settings for CDNs
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_CDN_Setting extends Caldera_Forms_Settings_Option {

	/**
	 * Get the defaults
	 *
	 * @since 1.5.3
	 *
	 * @var array
	 */
	protected $defaults = array(
		'cdn'     => false,
		'combine' => false,
	);

	/**
	 * Get allowed CDNs
	 *
	 * @since 1.5.3
	 *
	 * @var array
	 */
	protected $cdns = array(
		'jsdelivr'
	);

	/**
	 * @inheritdoc
	 * @since 1.5.3
	 */
	public function get_name(){
		return 'cdn';
	}

	/**
	 * @inheritdoc
	 * @since 1.5.3
	 */
	public function get_option_key(){
		return '_caldera_forms_cdn_settings';
	}


	/**
	 * @inheritdoc
	 * @since 1.5.3
	 */
	protected function prepare( array $settings ){
		foreach ( $settings as $key => $value ){
			if( ! array_key_exists( $key, $this->defaults ) ){
				unset( $settings[ $key ] );
				continue;
			}

			if( 'cdn' === $key && ! in_array( $value, $this->cdns ) ){
				$settings[ $key ] = $this->defaults[ 'cdn' ];
			}

		}


		return wp_parse_args( $settings, $this->defaults );

	}


	/**
	 * @inheritdoc
	 * @since 1.5.3
	 */
	protected function get_defaults(){
		return $this->defaults;
	}


}