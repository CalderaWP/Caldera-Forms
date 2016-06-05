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
class Caldera_Forms_Email_SendGrid extends Caldera_Forms_Email_Client{


	public function include_sdk() {
		include_once __DIR__ . '/sendgrid/sendgrid-php.php';
	}

	public function set_api(){
		$options = array(
			'raise_exceptions' => true
		);

		if( ! is_ssl() ){
			$options[ 'turn_off_ssl_verification' ] = true;
		}

		$options = apply_filters( 'caldera_forms_sendgrid_options', $options  );

		$this->api =  new \SendGrid( get_option( '_caldera_forms_send_grid_api_key', 0 ));
	}

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
			$this->api->send($email);
		} catch(\SendGrid\Exception $e) {
			//echo $e->getCode() . "\n";
			foreach($e->getErrors() as $er) {
				//echo $er;
			}
		}
	}
}
