<?php
/**
 * Base class for settings stored as an array in options table
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */

abstract class Caldera_Forms_Settings_Option implements Caldera_Forms_Settings_Contract{

	/**
	 * Get the saved settings
	 *
	 * @since 1.5.3
	 *
	 * @return array
	 */
	public function get_settings(){
		$settings = get_option( $this->get_option_key(), $this->get_defaults() );
		return $this->prepare( $settings );
	}

	/**
	 * Get the saved settings
	 *
	 * @since 1.5.3
	 *
	 * @param array $settings Settings to save
	 *
	 * @return array
	 */
	public function save_settings( array $settings ){
		$settings = $this->prepare( $settings );
		update_option( $this->get_option_key(), $settings );
		return $settings;
	}

	/**
	 * Get name for option key storing setting
	 *
	 * @since 1.5.3
	 *
	 * @return string
	 */
	public function get_option_key(){
		//must ovveride, should be abstract but PHP 5.2, so this hack
		_doing_it_wrong( __FUNCTION__, '', '1.5.3' );
	}

	/**
	 * Validate and sanitize settings
	 *
	 * @since 1.5.3
	 *
	 * @param array $settings Settings to save
	 *
	 * @return array
	 */
	protected function prepare( array $settings ){
		//must ovveride, should be abstract but PHP 5.2, so this hack
		_doing_it_wrong( __FUNCTION__, '', '1.5.3' );
		return $settings;
	}

	/**
	 * Get default settings
	 *
	 * @since 1.5.3
	 *
	 * @return array
	 */
	protected function get_defaults(){
		//must ovveride, should be abstract but PHP 5.2, so this hack
		_doing_it_wrong( __FUNCTION__, '', '1.5.3' );
		return array();
	}

}