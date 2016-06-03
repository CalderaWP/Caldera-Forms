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
class Caldera_Forms_Mail_Sendgrid extends Caldera_Forms_Mail_API {

	protected $mail_data;

	protected $api_key;

	protected $sent;

	protected $client;

	public function __construct( Caldera_Forms_Mail_Data $mail_data ) {
		$this->mail_data = $mail_data;
		$this->api_key = Caldera_Forms_Mail_Keys::get_instance()->get_key( 'sendgrid' );
		if( ! $this->api_key ){
			$this->sent = false;
		}else{
			include_once __DIR__ . '/sendgrid/lib/SendGrid';
			$this->set_client();
		}
	}

	protected function set_client(){
		$options = array(
			'raise_exceptions' => true
		);

		if( ! is_ssl() ){
			$options[ 'turn_off_ssl_verification' ] = false;
		}
		$this->client = new SendGrid( $this->api_key, $options );
	}

	public function sent(){
		return $this->sent;
	}




	public function send(){
		$email = new SendGrid\Email();
		$email
			->setFrom($this->mail_data->get_header( 'from' ) )
			->setSubject('Subject goes here')
			->setText('Hello World!')
			->setHtml('<strong>Hello World!</strong>')
		;
		
		foreach( $this->mail_data->get_recipients() as $recipient ){
			$email->addTo( $recipient  );
		}

		try {
			$this->client->send($email);
		} catch(\SendGrid\Exception $e) {
			echo $e->getCode() . "\n";
			foreach($e->getErrors() as $er) {
				echo $er;
			}
		}





		//api_user=your_sendgrid_username&api_key=your_sendgrid_password&to=destination@example.com&toname=Destination&subject=Example_Subject&text=testingtextbody&from=info@domain.com
	}

}
