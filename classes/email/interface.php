<?php

/**
 * Interface for email API clients
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
interface Caldera_Forms_Email_Interface {

	/**
	 * Set API keys
	 *
	 * @since 1.3.6
	 *
	 * @param array $keys
	 */
	public function set_api( array $keys );

	/**
	 * Send email
	 *
	 * @since 1.3.6
	 *
	 * @return array|int Array of errors or status code
	 */
	public function send();

	/**
	 * Include SDK for API
	 *
	 * @since 1.3.6
	 */
	public function include_sdk();
}
