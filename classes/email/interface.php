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
interface Caldera_Forms_Email_Interface {

	public function set_api();

	public function send();

	public function include_sdk();
}
