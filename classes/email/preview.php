<?php

/**
 * Creates an email preview
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Email_Preview extends Caldera_Forms_Email_Save {

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return array(
			'headers' => $this->headers(),
			'message' => $this->body(),
			'content-type' => $this->content_type()

		);

	}
	
}