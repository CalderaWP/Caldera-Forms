<?php

/**
 * Interface settings objects must implement
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
interface Caldera_Forms_Settings_Contract {

	/**
	 * Get name for setting
	 *
	 * @since 1.5.3
	 *
	 * @return string
	 */
	public function get_name();

}