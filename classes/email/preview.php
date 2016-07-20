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
class Caldera_Forms_Email_Preview implements JsonSerializable {

	/**
	 * The mail data
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $mail;


	/**
	 * Caldera_Forms_Email_Preview constructor.
	 *
	 * @since 1.4.0
	 *
	 * @param array $mail Mail data
	 */
	public function __construct( $mail ){
		$this->mail = $mail;
		
	}

	/**
	 * Get content type
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public function content_type(){
		if( isset( $this->mail[ 'html' ] ) && $this->mail[ 'html' ] ){
			return 'text/html';
		}

		return 'text/plain';
	}

	/**
	 * Get email headers
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	public function headers(){
		$headers = array();
		$headers[ 'recipients' ]  = $this->mail[ 'recipients' ];
		$headers[ 'subject' ] = $this->mail[ 'subject' ];
		$headers[ 'all' ] = $this->mail[ 'headers' ];

		return $headers;
	}

	/**
	 * Get the email message
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public function body(){
		if( isset( $this->mail[ 'message' ] ) ){
			return $this->mail[ 'message' ];
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return  array(
			'headers' => $this->headers(),
			'message' => $this->body(),
			'content-type' => $this->content_type()

		);

	}
	
}