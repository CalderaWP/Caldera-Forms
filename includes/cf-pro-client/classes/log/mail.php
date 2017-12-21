<?php


namespace calderawp\calderaforms\pro\log;
use calderawp\calderaforms\pro\container;
use Monolog\Logger;

/**
 * Class mail
 *
 * Handles mail log mode logging
 *
 * @package calderawp\calderaforms\pro\log
 */
class mail{

	/**
	 * Handles logging after the message (should) be sent
	 *
	 * @since 1.1.0
	 *
	 * @param int $entry_id The entry ID
	 * @param string $form_id The form ID
	 * @param \calderawp\calderaforms\pro\settings\form $form_settings Form settings
	 */
	public function before( $mail, $entry_id, $form_id, $form_settings ){
		$settings = container::get_instance()->get_settings();

		$data = [
			'mail_to' => ! empty( $mail[ 'recipients' ] ) ? $mail[ 'recipients' ] : '',
			'entry_id' => $entry_id,
			'form_id' => $form_id,
			'form_settings' => $form_settings->toArray(),
			'send_local' => (bool) $form_settings->should_send_local(),
			'enhanced_delivery' => is_object( $settings ) ? $settings->get_enhanced_delivery() : 'settings object is not an object :('

		];

		$this->log( $data, current_filter() );

	}

	/**
	 * Handles logging after the message (should) have been sent
	 *
	 * @since  1.1.0
	 *
	 * @param  \calderawp\calderaforms\pro\message|\WP_Error $message Message Object or error
	 * @param int $entry_id The entry ID
	 * @param string $form_id The form ID
	 */
	public function after( $message, $entry_id, $form_id ){

		$data = [
			'error' => is_wp_error( $message ) ? true : false,
			'entry_id' => $entry_id,
			'form_id' => $form_id,
			'cfp_id' => ! is_wp_error( $message ) ? $message->get_cfp_id() : 0,
			'local_id' => ! is_wp_error( $message ) ? $message->get_entry_id() : 0,
		];

		$this->log( $data, current_filter() );
	}

	/**
	 * Send log entry
	 *
	 * @since 1.1.0
	 *
	 * @param array $data Log data
	 * @param string $message Log message
	 */
	protected function log($data, $message ){
		container::get_instance()->get_logger()->send(
			$message,
			$data,
			Logger::INFO
		);
	}

}