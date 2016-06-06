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
class Caldera_Forms_Email_SendGrid extends Caldera_Forms_Email_Client{

	/**
	 * @inheritdoc
	 */
	public function include_sdk() {
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
		$email = new \SendGrid\Email();
		$email
			->setFrom( $this->message[ 'from' ] )
			->setSubject( $this->message[ 'subject' ] );

		if( is_array( $this->message[ 'recipients'] ) ){
			foreach ( $this->message[ 'recipients'] as $recipient ) {
				//@TODO validate is email. Deal with it being name: email format
				$email->addTo( $recipient );
			}
		}
		
		if( $this->message[ 'html' ] ){
			$email->setHtml( $this->message[ 'message' ] );
		}else{
			$email->setText( $this->message[ 'message' ] );
		}

		//@TODO seperate name/email ?
		if( is_email( $this->message[ 'bcc' ] ) ){
			$email->setBcc( $this->message[ 'bcc' ] );
		}

		if( is_email( $this->message[ 'replyto' ] )  ){
			$email->setReplyTo( $this->message[ 'replyto' ] );
		}

		$email = apply_filters( 'caldera_forms_sendgrid_before', $email, $this->message  );

		//@TODO Caldera Exception
		try {
			/** @var \SendGrid\Response $response */
			$response = $this->api->send($email);
			return $response->getCode();
		} catch(\SendGrid\Exception $e) {
			$errors[] = $e->getCode();
			foreach($e->getErrors() as $er) {
				$errors[] =  $er;
			}
			return $errors;
		}
	}
	
}
