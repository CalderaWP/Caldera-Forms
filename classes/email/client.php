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
	 * @since 1.4.0
	 *
	 * @var object
	 */
	protected $api;

	/**
	 * Message details
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $message;

	/**
	 * Message attachments
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $attachments;

	/**
	 * Caldera_Forms_Email_Client constructor.
	 *
	 * @since 1.4.0
	 *
	 * @param array $message Message details
	 */
	public function __construct( array $message ) {
		$this->include_sdk();

		$this->message = $message;

		$this->prepare_attachments();

	}

	/**
	 * Create  Caldera_Forms_Email_Attachment objects
	 *
	 * @since 1.4.0
	 */
	public function prepare_attachments(){
		if( ! empty( $this->message[ 'attachments' ] ) ) {
			foreach ( $this->message['attachments'] as $attachment ) {
				$obj = new Caldera_Forms_Email_Attachment( );
				$obj->content = $attachment;
				$this->attachments[] = $obj;
			}

		}

	}
	
}
