<?php

/**
 * CDN integrations for jsdelivr
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */

class Caldera_Forms_CDN_Jsdelivr extends Caldera_Forms_CDN {

	/**
	 * @inheritdoc
	 * @since 1.5.3
	 */
	public function cdn_url( ){
		return '//cdn.jsdelivr.net/wp/caldera-forms/tags/' . $this->version . '/';
	}

	/**
	 * @inheritdoc
	 * @since 1.5.3
	 */
	public function add_hooks(){
		add_action( 'wp_footer', array( $this, 'radar_tag' ) );
		parent::add_hooks();
	}

	/**
	 * @inheritdoc
	 * @since 1.5.3
	 */
	public function remove_hooks(){
		remove_action( 'wp_footer', array( $this, 'radar_tag' ) );
		parent::remove_hooks();
	}

	/**
	 * Add the radar tag for CDN network
	 *
	 * @see: https://github.com/jsdelivr/jsdelivr#contribute-performance-data
	 *
	 * @uses "wp_footer"
	 *
	 * @since 1.5.3
	 */
	public function radar_tag(){
		echo '<script async src="' . $this->protocol . '//radar.cedexis.com/1/11475/radar.js"></script>';
	}

}