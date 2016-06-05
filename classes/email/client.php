<?php

/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
abstract class Caldera_Forms_Email_Client implements Caldera_Forms_Email_Interface {

	protected $api;

	protected $message;

	public function __construct( array $message ) {

		$this->message = $message;
		$this->set_api();
	}

	

}
