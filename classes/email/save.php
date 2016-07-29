<?php

/**
 * Creates a JSON serializable object to save emails
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract class Caldera_Forms_Email_Save implements JsonSerializable {

	/**
	 * The mail data
	 *
	 * @since 1.4.1
	 *
	 * @var array
	 */
	protected $mail;


	/**
	 * Caldera_Forms_Email_Preview constructor.
	 *
	 * @since 1.4.1
	 *
	 * @param array $mail Mail data
	 */
	public function __construct( $mail ){
		$this->mail = $mail;

	}

	/**
	 * Get the raw mail message array
	 * 
	 * @since 1.4.1
	 * 
	 * @return array
	 */
	public function get_mail(){
		return $this->mail;
	}

	/**
	 * Get content type
	 *
	 * @since 1.4.1
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
	 * @since 1.4.1
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
	 * @since 1.4.1
	 *
	 * @return string
	 */
	public function body(){
		if( isset( $this->mail[ 'message' ] ) ){
			return $this->mail[ 'message' ];
		}

		return '';
	}


}