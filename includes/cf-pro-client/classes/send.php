<?php


namespace calderawp\calderaforms\pro;
use calderawp\calderaforms\pro\api\client;
use calderawp\calderaforms\pro\exceptions\Exception;


/**
 * Class send
 *
 * Class to handle turning Caldera Forms' $mail array into message objects and send them,
 *
 * @package calderawp\calderaforms\pro
 */
class send {

	/**
	 * Send main mailer to CF Pro
	 *
	 * @since 0.0.1
	 *
	 * @param array $mail the array provided on "caldera_forms_mailer" filter
	 * @param int $entry_id The entry ID
	 * @param string $form_id The form ID
	 * @param bool $send Optional. If message should be sent. Default is true. If false, will be stored but not sent.
	 *
	 * @return \calderawp\calderaforms\pro\message|array
	 */
	public static function main_mailer( $mail, $entry_id, $form_id, $send = true ){


		$form_settings = container::get_instance()->get_settings()->get_form( $form_id );
		if ( ! $form_settings ) {
			return $mail;
		}

		$message = new \calderawp\calderaforms\pro\api\message();

		$message->add_recipient( 'reply', $mail[ 'from' ],  $mail[ 'from_name' ] );

		if( is_string( $mail[ 'recipients' ] ) ){
			$message->add_recipient( 'to', $mail[ 'recipients' ] );
		}elseif ( is_array( $mail[ 'recipients' ] ) && ! empty( $mail[ 'recipients' ] ) ) {
			foreach ( $mail[ 'recipients' ] as $to ){
				$message->add_recipient( 'to', $to );
			}

		}

		$message->subject = $mail[ 'subject' ];
		$message->content = $mail[ 'message' ];
		$message->pdf_layout = $form_settings->get_pdf_layout();
		$message->layout = $form_settings->get_layout();
		if ( ! empty( $mail[ 'cc' ] ) ) {
			$ccs = caldera_forms_safe_explode( $mail[ 'cc' ] );
			foreach ( $ccs as $cc ){
				$message->add_recipient( 'cc', $cc );

			}

		}

		if( ! empty( $mail[ 'bcc' ] ) ){
			$bccs = caldera_forms_safe_explode( $mail[ 'bcc' ] );
			foreach ( $bccs as $bcc ){
				$message->add_recipient( 'bcc', $bcc );

			}
		}

		if( $form_settings->should_attatch_pdf() ){
			$message->pdf = true;
		}

		if( isset( $mail[ 'attachments' ] ) && ! empty( $mail[ 'attachments'])){
			foreach ( $mail[ 'attachments'] as $attachment ) {
				$message = $message->add_attachment( $attachment );
			}
		}
		$message->entry_id = $entry_id;
		$message->add_entry_data( $entry_id, \Caldera_Forms_Forms::get_form( $form_id ) );

		$response = self::send_via_api( $message, $entry_id, $send );

		return $response;

	}

	/**
	 * Handle attaching PDFs
	 *
	 * @since 0.0.1
	 *
	 * @param message $message
	 * @param array $mail
	 *
	 * @return array
	 */
	public static function attatch_pdf( message $message, array $mail  ){
		$uploader = new pdf( $message );
		$file = $uploader->upload();
		if( ! empty( $file ) ){
			$mail[ 'attachments' ][] = $file;
		}

		add_action( 'caldera_forms_mailer_complete', array( $uploader, 'delete_file' ) );
		add_action( 'caldera_forms_mailer_failed', array( $uploader, 'delete_file' ) );

		wp_schedule_single_event( time() + 599, pdf::CRON_ACTION, array( $file ) );
		return $mail;
	}

	/**
	 * Send message via API
	 *
	 * @since 0.0.1
	 *
	 * @param api\message $message Message object
	 * @param int $entry_id Entry ID for message
	 * @param bool $send If true app will store and send. If false, only store.
	 * @param string $type Optional. The message type. Default is "main" Options: main|auto
	 *
	 * @return message|null|\WP_Error
	 */
	public static function send_via_api( \calderawp\calderaforms\pro\api\message $message, $entry_id, $send,  $type = 'main' ){
		$client   = container::get_instance()->get_api_client();
		$response = $client->create_message( $message, $send, $entry_id, $type );

		return $response;
	}

}