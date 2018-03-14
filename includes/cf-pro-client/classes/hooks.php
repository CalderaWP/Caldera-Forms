<?php


namespace calderawp\calderaforms\pro;
use calderawp\calderaforms\pro\admin\menu;
use calderawp\calderaforms\pro\admin\scripts;
use calderawp\calderaforms\pro\api\client;
use calderawp\calderaforms\pro\api\local\files;
use calderawp\calderaforms\pro\api\local\settings;
use calderawp\calderaforms\pro\api\message;
use calderawp\calderaforms\pro\log\mail;
use calderawp\calderaforms\pro\settings\active;


/**
 * Class hooks
 *
 * Handles interaction with WordPress plugins API
 *
 * @package calderawp\calderaforms\pro
 */
class hooks {

	/**
	 * Add hooks needed for CF Pro
	 *
	 * @since 1.5.8
	 */
	public function add_hooks(){
		add_action( 'caldera_forms_rest_api_pre_init', array( $this, 'init_api' ) );

		if( active::get_status() ){
			add_filter( 'caldera_forms_mailer', array( $this, 'mailer' ), 99, 4 );
			add_filter( 'caldera_forms_ajax_return', array( $this, 'add_pdf_link_ajax' ), 10, 2 );
			add_filter( 'caldera_forms_render_notices', array( $this, 'add_pdf_link_not_ajax' ), 10, 2 );
			add_filter( 'caldera_forms_autoresponse_mail', array( $this, 'auto_responder' ), 99, 4 );
			add_action( 'caldera_forms_pro_loaded', array( $this, 'init_logger' ) );
			add_action( 'caldera_forms_checked_tables', array( $this, 'capture_tables_object' ) );
			add_action( 'caldera_forms_rest_api_pre_init', array( $this, 'init_file_api' ) );

		}


		add_action( pdf::CRON_ACTION, array( $this, 'delete_file' ) );

	}

	/**
	 * Remove hooks needed for CF Pro
	 *
	 * @since 1.5.8
	 */
	public function remove_hooks(){
		remove_filter( 'caldera_forms_mailer', array( $this, 'mailer' ), 10 );
		remove_action( 'caldera_forms_rest_api_pre_init', array( $this, 'init_api' ) );
		remove_filter( 'caldera_forms_ajax_return', array( $this, 'add_pdf_link_ajax' ), 10 );
		remove_filter( 'caldera_forms_render_notices', array( $this, 'add_pdf_link_not_ajax' ), 10);
		remove_filter( 'caldera_forms_autoresponse_mail', array( $this, 'auto_responder' ), 99 );
		remove_action( pdf::CRON_ACTION, array( $this, 'delete_file' ) );

	}

	/**
	 * Intercept emails and send to remote app if called for
	 *
	 * @uses "caldera_forms_mailer" filter
	 *
	 * @sine 1.5.8
	 *
	 * @param $mail
	 * @param $data
	 * @param $form
	 * @param $entry_id
	 *
	 * @return null|array
	 */
	public function mailer( $mail, $data, $form, $entry_id ){
		$form_settings = container::get_instance()->get_settings()->get_form( $form[ 'ID' ] );
        $entry_id = $this->maybe_fix_entry_id( $entry_id );

		/**
		 * Runs before main mailer is handled by CF Pro
		 *
		 * @since  1.5.8
		 *
		 * @param array $mail the array provided on "caldera_forms_mailer" filter
		 * @param int $entry_id The entry ID
		 * @param string $form_id The form ID
         * @param \calderawp\calderaforms\pro\settings\form $form_settings Form settings
		 */
		do_action( 'caldera_forms_pro_before_main_mailer', $mail, $entry_id, $form[ 'ID' ], $form_settings );

		if ( ! $form_settings ) {
			return $mail;
		}

		$send_local = $form_settings->should_send_local();
		$send_remote = ! $send_local;
        if ( $send_remote ) {
            $sent_message = send::main_mailer($mail, $entry_id, $form ['ID'], $send_remote);
        } else {
            $sent_message = null;
        }

		/**
		 * Runs after main mailer is handled by CF Pro
		 *
		 * @since  1.5.8
		 *
		 * @param  \calderawp\calderaforms\pro\message|\WP_Error|null $sent_message Message Object or error or null if not sending via CF Pro.
		 * @param int $entry_id The entry ID
		 * @param string $form_id The form ID
		 */
		do_action( 'caldera_forms_pro_after_main_mailer', $sent_message, $entry_id, $form [ 'ID' ]  );

		if ( is_object( $sent_message ) && ! is_wp_error( $sent_message ) ) {
			if ( $send_local &&  $form_settings->should_attatch_pdf() ) {
				$mail = send::attatch_pdf( $sent_message, $mail );
			}
		}else{
			return $mail;

		}

		if ( $send_local ) {
			if (  $form_settings->use_html_layout() ) {
				$mail[ 'message' ] = $sent_message->get_html();
			}

			return $mail;
		}

		return null;

	}

	/**
	 * Handle autoresponder emails
	 *
	 * @sine 1.5.8
	 *
	 * @uses "caldera_forms_autoresponse_mail" filter
	 *
	 * @param $mail
	 * @param $config
	 * @param $form
	 * @param $entry_id
	 *
	 * @return null
	 */
	public function auto_responder( $mail, $config, $form, $entry_id ){
		$form_settings = container::get_instance()->get_settings()->get_form( $form[ 'ID' ] );
        $entry_id = $this->maybe_fix_entry_id( $entry_id );

		/**
		 * Runs before autoresponder is handled by CF Pro
		 *
		 * @since  1.5.8
		 *
		 * @param array $mail the array provided on "caldera_forms_mailer" filter
		 * @param int $entry_id The entry ID
		 * @param string $form_id The form ID
		 * @param \calderawp\calderaforms\pro\settings\form $form_settings Form settings
		 */
		do_action( 'caldera_forms_pro_before_auto_responder', $mail, $entry_id, $form[ 'ID' ], $form_settings );


		if ( ! $form_settings ) {
			return $mail;
		}
		$send_local = $form_settings->should_send_local();
		$send_remote = ! $send_local;

        if ( $send_remote ) {
            $message = new \calderawp\calderaforms\pro\api\message();
            $message->add_recipient('reply',
                \Caldera_Forms::do_magic_tags($config['sender_email']),
                \Caldera_Forms::do_magic_tags($config['sender_name'])
            );

            if( is_array( $mail ['recipients'])){
                foreach ( $mail[ 'recipients' ] as $recipient ){
                    $message->add_recipient('to',
                        $recipient['email'],
                        $recipient['name']
                    );
                }
            }else{
                $message->add_recipient('to',
                    \Caldera_Forms::do_magic_tags($config['recipient_email']),
                    \Caldera_Forms::do_magic_tags($config['recipient_name'])
                );
            }


            if (!empty($mail['cc'])) {
                if (is_string($mail['cc'])) {
                    $mail['cc'] = explode(',', $mail['cc']);
                    foreach ($mail['cc'] as $cc) {
                        if (is_email(trim($cc))) {
                            $message->add_recipient('cc', trim($cc));
                        }
                    }
                }
            }

            if (!empty($mail['bcc'])) {
                if (is_string($mail['bcc'])) {
                    $mail['bcc'] = explode(',', $mail['bcc']);
                    foreach ($mail['bcc'] as $bcc) {
                        if (is_email(trim($bcc))) {
                            $message->add_recipient('bcc', trim($bcc));
                        }
                    }
                }
            }

            $message->subject = $mail ['subject'];
            $message->content = $mail['message'];
            $message->pdf_layout = $form_settings->get_pdf_layout();
            $message->layout = $form_settings->get_layout();
            $message->add_entry_data($entry_id, $form);
            $message->entry_id = $entry_id;
            $this->maybe_anti_spam( $message, $form );
            $sent_message = send::send_via_api($message, $entry_id, $send_remote, 'auto');
        } else {
            $sent_message = null;
        }

		/**
		 * Runs after autoresponder is handled by CF Pro
		 *
		 * @since  1.5.8
		 *
		 * @param  \calderawp\calderaforms\pro\message|\WP_Error|null $sent_message Messsage Object or error or null if not sending via CF Pro.
		 * @param int $entry_id The entry ID
		 * @param string $form_id The form ID
		 */
		do_action( 'caldera_forms_pro_after_auto_responder', $sent_message, $entry_id, $form [ 'ID' ]  );


		if( is_object( $sent_message ) && ! is_wp_error( $sent_message ) ){
			if( $send_local ){
				return $mail;
			}else{
				return null;
			}
		}

		return $mail;
	}

	/**
	 * Add the PDF link to AJAX response
	 *
	 * @since 1.5.8
	 *
	 * @uses "caldera_forms_ajax_return" filter
	 *
	 * @param array $out Response data
	 * @param array $form Form config
	 *
	 * @return mixed
	 */
	public function add_pdf_link_ajax( $out, $form ){
		if( isset( $data[ 'cf_er' ] )  ){
			return $out;
		}

		$settings = container::get_instance()->get_settings()->get_form( $form['ID'] );
		if( ! $settings ||!  $settings->should_add_pdf_link() ){
			return $out;
		}

		$entry_id = $out[ 'data' ][ 'cf_id' ];
		$message = container::get_instance()->get_messages_db()->get_by_entry_id( $entry_id );
		if( $message && ! is_wp_error( $message ) ) {
			$link = $message->get_pdf_link();
			if( filter_var( $link, FILTER_VALIDATE_URL ) ) {
				$out[ 'html' ] .= caldera_forms_pro_link_html( $form, $link );
			}

		}

		return $out;

	}

	/**
	 * Add link to success messages when the form is NOT submitted via AJAX
	 *
	 * @since 1.5.8
	 *
	 * @uses "caldera_forms_render_notices" filter
	 *
	 * @param array $notices
	 * @param $form
	 *
	 * @return array
	 */
	public function add_pdf_link_not_ajax( $notices, $form ){
		if ( ! isset( $_GET[ 'cf_id' ] ) || ! isset( $_GET[ 'cf_su' ] ) ) {
			return $notices;
		}
		$entry_id = absint( $_GET[ 'cf_id' ] );
		$message = container::get_instance()->get_messages_db()->get_by_entry_id( $entry_id );
		if( $message && ! is_wp_error( $message ) ) {
			$link = $message->get_pdf_link();
			if( filter_var( $link, FILTER_VALIDATE_URL ) ){

				$html = caldera_forms_pro_link_html( $form, $link );
				if( isset( $notices[ 'success' ], $notices[ 'success' ][ 'note' ] ) ){
					$notices[ 'success' ][ 'note' ] = '<div class="cf-pro-pdf-link alert alert-success">' . $notices[ 'success' ][ 'note' ] . '</div>' . $html;
				}else{
					$notices[ 'success' ][ 'note' ] = $html;
				}

			}
		}

		return $notices;

	}


	/**
	 * Sets up CF REST API endpoint for the settings
	 *
	 * @since 1.5.8
	 *
	 * @uses "caldera_forms_rest_api_pre_init" action
	 *
	 * @param \Caldera_Forms_API_Load api
	 */
	public function init_api( $api ){
		$api->add_route( new settings() );
	}

	/**
	 * Sets up CF REST API endpoint for files
	 *
	 * @since 1.5.8
	 *
	 * @uses "caldera_forms_rest_api_pre_init" action
	 *
	 * @param \Caldera_Forms_API_Load api
	 */
	public function init_file_api( $api ){
		$api->add_route( new files() );
	}

	/**
	 * Delete a file
	 *
	 * @since 1.5.8
	 *
	 * Uses cron action set in pdf::CRON_ACTION
	 *
	 * @param string $file Absolute path to file to be deleted
	 */
	public function delete_file( $file ){
		unlink( $file );
	}

	/**
	 * Initialize remove logger
	 *
	 * @since 1.5.8
	 */
	public function init_logger(){
		/**
		 * Enables mail debug log mode
         *
         * @since 1.1.0
		 */
	    if( apply_filters( 'caldera_forms_pro_mail_debug', false ) ){
	        $this->init_mail_log();
        }

		/**
		 * Filter to disable Caldera Forms Pro log mode
         *
         * @since 1.5.8
		 *
		 * @param bool $param $use
		 */
		$use = apply_filters( 'caldera_forms_pro_log_mode', true );
		if( ! $use ){
			return;
		}
		if( ! is_object( container::get_instance()->get_tables() ) ){
			\Caldera_Forms::check_tables();
		}
		\Inpsyde\Wonolog\bootstrap( new \calderawp\calderaforms\pro\log\handler() );

	}

	/**
	 * Initializes mail log mode
     *
     * @since 1.1.0
	 */
	public function init_mail_log(){
	   $mail_logger = new mail();
	   add_action( 'caldera_forms_pro_before_auto_responder', [ $mail_logger, 'before' ], 10, 4 );
	   add_action( 'caldera_forms_pro_before_main_mailer', [ $mail_logger, 'before' ], 10, 4 );

	   add_action( 'caldera_forms_pro_after_auto_responder', [ $mail_logger, 'after' ], 10, 3 );
	   add_action( 'caldera_forms_pro_after_main_mailer', [ $mail_logger, 'after' ], 10, 3 );

    }

	/**
	 * Capture Caldera_Forms_DB_Tables object for reuse later
	 *
	 * @since 1.5.8
	 *
	 * @uses "caldera_forms_checked_tables"
	 *
	 * @param  \Caldera_Forms_DB_Tables $tables
	 */
	public function capture_tables_object( $tables ){
		container::get_instance()->set_tables( $tables );
	}

    /**
     * Sets anti-spam args for requests, if needed
     *
     * @since 1.6.0
     *
     * @param message $message Message to be sent
     * @param array $form Form config
     */
	public function maybe_anti_spam( \calderawp\calderaforms\pro\api\message $message, array $form ){
       $args = container::get_instance()->get_anti_spam_args();
       if( empty( $args )){
            $checker = new antispam( $message, $form);
            if( $checker->should_check() ){
                container::get_instance()->set_anti_spam_args( $checker->get_args() );
            }
       }
    }


    /**
     * Check the entry ID and if it is not numeric and one can be found in transdata use that.
     *
     * @since 1.6.0
     *
     * @see https://github.com/CalderaWP/Caldera-Forms/issues/2295#issuecomment-371325361
     *
     * @param int|null|false $maybe_id Maybe the entry ID
     * @return int|null
     */
    protected function maybe_fix_entry_id( $maybe_id ){
	    if( is_numeric( $maybe_id ) ){
	        return $maybe_id;
        }
        global $transdata;
        return is_array( $transdata ) && isset( $transdata[ 'entry_id' ] )
            ? $transdata[ 'entry_id' ] : null;
    }
}