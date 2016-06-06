<?php

/**
 * Base class for email API clients
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract class Caldera_Forms_Email_Client implements Caldera_Forms_Email_Interface {

	/**
	 * API object
	 *
	 * @since 1.3.6
	 *
	 * @var object
	 */
	protected $api;

	/**
	 * Message details
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $message;

	/**
	 * Caldera_Forms_Email_Client constructor.
	 *
	 * @param array $message Message details
	 */
	public function __construct( array $message ) {

		$this->message = $message;

	}
	
}
