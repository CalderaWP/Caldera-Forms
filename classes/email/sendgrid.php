<?php
/**
 * API Client for sending Caldera Forms emails via SendGrid
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

use \SendGrid\Mail;
use \SendGrid\Email;
use \SendGrid\Personalization;
use \SendGrid\Content;
use \SendGrid\Attachment;
use \SendGrid\TrackingSettings;
use \SendGrid\ClickTracking;
use \SendGrid\OpenTracking;
use \SendGrid\ReplyTo;

class Caldera_Forms_Email_SendGrid extends Caldera_Forms_Email_Client{

	/**
	 * Track emails we are sending to
	 * 
	 * SendGrid will not let you send to same address twice
	 * 
	 * @since 1.4.0
	 * 
	 * @var array
	 */
	protected $sent_to = array();

	/**
	 * @inheritdoc
	 */
	public function include_sdk() {
		if( class_exists('SendGrid') ){
			return;
		}
		include_once __DIR__ . '/sendgrid/sendgrid-php.php';
	}

	/**
	 * @inheritdoc
	 */
	public function set_api( array $keys ){
		$options = array(
			'raise_exceptions' => true
		);

		if( ! is_ssl() ){
			$options[ 'turn_off_ssl_verification' ] = true;
		}

		$options = apply_filters( 'caldera_forms_sendgrid_options', $options  );

		$this->api =  new \SendGrid( $keys[0], $options );
	}

	/**
	 * @inheritdoc
	 */
	public function send(){

		$mail = new Mail();

		$email = $this->create_email( $this->message[ 'from' ], $this->message[ 'from_name' ], false );
		$mail->setFrom( $email );
		
		$mail->setSubject( $this->message[ 'subject' ] );
		$personalization = new \SendGrid\Personalization();

		foreach ( $this->message[ 'recipients' ] as $recipient ){

			if( ! is_email( $recipient ) ){
				$open = strpos( $recipient, '<' );
				$close = strpos( $recipient, '>' );
				$length = strlen( $recipient );
				if( is_numeric( $open ) && $length - 1 == $close ){
					$recipient = substr( $recipient, $open + 1, $close);
					$recipient = str_replace( array( '<', '>' ), '', $recipient  );
				}

			}

			if ( is_email( $recipient ) ) {
				$email = $this->create_email( $recipient );
				if ( is_object( $email ) ) {
					$personalization->addTo( $email );
				}
			}
		}
		
		if( ! empty( $this->message[ 'bcc' ] ) && is_email( $this->message[ 'bcc' ] ) ){
			$email = $this->create_email( $this->message[ 'bcc' ] );
			if ( is_object( $email ) ) {
				$personalization->addTo( $email );
			}
			
		}
		
		$mail->addPersonalization( $personalization );
		
		$content = new Content("text/html", $this->message[ 'message' ] );
		
		$mail->addContent( $content );
		
		if( ! empty( $this->attachments ) ){
			/* @var Caldera_Forms_Email_Attachment $cf_attachment_obj */
			foreach ( $this->attachments as $cf_attachment_obj ) {
				$attachment = new Attachment();
				$attachment->setContent( $cf_attachment_obj->get_encoded() );
				$attachment->setType( $cf_attachment_obj->type );
				$attachment->setFilename( $cf_attachment_obj->filename );
				$attachment->setDisposition( 'attachment' );
				$attachment->setContentId( md5( $cf_attachment_obj->filename ) );
				$mail->addAttachment( $attachment );
			}
			
		}
		
		$tracking_settings = new \SendGrid\TrackingSettings();
		$click_tracking = new ClickTracking();
		$click_tracking->setEnable( true );
		$click_tracking->setEnableText( true );
		$tracking_settings->setClickTracking( $click_tracking );
		$open_tracking = new OpenTracking();
		$open_tracking->setEnable(true);
		$tracking_settings->setOpenTracking( $open_tracking );
		$mail->setTrackingSettings( $tracking_settings );

		if( ! empty( $this->message[ 'replyto' ] ) && is_email( $this->message[ 'replyto' ] ) ){
			$reply_to = new ReplyTo( $this->message[ 'replyto' ] );
			$mail->setReplyTo ($reply_to );
		}


		/**
		 * Modify SendGrid mail object before sending to remote API
		 *
		 * @since 1.4.0
		 *
		 * @param \SendGrid\Mail $mail SendGrid SDK email object
		 * @param array $message Raw message details
		 */
		$mail = apply_filters( 'caldera_forms_sendgrid_before', $mail, $this->message );

		//@TODO Caldera Exception
		try {
			/** @var \SendGrid\Response $response */
			$response = $this->api->client->mail()->send()->post( $mail );
			return $response->statusCode();
		} catch(\SendGrid\Exception $e) {
			$errors[] = $e->getCode();
			foreach($e->getErrors() as $er) {
				$errors[] =  $er;
			}
			return $errors;
		}
	}

	/**
	 * Creates SendGrid email object
	 * 
	 * Main reason for this is to prevent adding duplicated send tos, which will cause API error.
	 * 
	 * @since 1.4.0
	 * 
	 * @param string $address Email address to send to.
	 * @param string|null $name Optional. Name of who sending to. Default is null.
	 * @param bool $prevent_double Optional. If true, prevent duplicates by returing false. Default is true.
	 *
	 * @return Email
	 */
	protected function create_email( $address, $name = null, $prevent_double = true ){
		if ( is_email( $address ) ) {
			if ( false == $prevent_double || ! in_array( $address, $this->sent_to ) ) {
				$this->sent_to[] = $address;
				return new Email( $name, $address );

			}

		}
		
		
	}
	
}
