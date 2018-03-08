<?php
/**
 * Handles final saving of the form.
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 CalderaWP LLC
 */
class Caldera_Forms_Save_Final {

	/**
	 * Save form in database
	 *
	 * @since 1.2.3
	 *
	 * @param array $form Form config
	 * @param int|null $entryid Optional. ID of entry to send. If not provided, will be determined from current POST data '_entry_id' key.
	 */
	public static function save_in_db( $form, $entryid = null ) {
		global $wpdb, $processed_meta;
		if(!empty($form['db_support'])){

			// new entry or update
			if(empty($entryid)){

				$entryid = Caldera_Forms::get_field_data( '_entry_id', $form );

				foreach($form['fields'] as $field_id=>$field){
					// add new and update
					Caldera_Forms::save_field_data($field, $entryid, $form);

				}

				// save form meta if any
				if(isset($processed_meta[$form['ID']])){
					foreach($processed_meta[$form['ID']] as $process_id=>$meta_data){

						foreach($meta_data as $meta_key=>$meta_value){
							if(is_array($meta_value)){
								foreach ($meta_value as &$check_value) {
									if(is_array($check_value)){
										foreach($check_value as &$sub_check_value){
											if(!is_array($sub_check_value)){
												$sub_check_value = Caldera_Forms::do_magic_tags( $sub_check_value );
											}
										}
									}else{
										$check_value = Caldera_Forms::do_magic_tags( $check_value );
									}
								}
							}else{
								$meta_value = Caldera_Forms::do_magic_tags( $meta_value );
							}


							if(count($meta_value) > 1){
								$meta_value = json_encode($meta_value);
							}else{
								$meta_value = $meta_value[0];
								if(is_array($meta_value) || is_object($meta_value)){
									$meta_value = json_encode($meta_value);
								}
							}

							$meta_entry = array(
								'entry_id'	 =>	$entryid,
								'process_id' => $process_id,
								'meta_key'	 =>	$meta_key,
								'meta_value' =>	$meta_value
							);

							$wpdb->insert($wpdb->prefix . 'cf_form_entry_meta', $meta_entry);

						}

					}
				}

				// update status
				$wpdb->update($wpdb->prefix . 'cf_form_entries', array('status' => 'active'), array( 'id' => $entryid ) );

			}else{

				// do update
				foreach($form['fields'] as $field_id=>$field){
					// add new and update
					Caldera_Forms::update_field_data($field, $entryid, $form);

				}

				if(isset($processed_meta[$form['ID']])){
					foreach($processed_meta[$form['ID']] as $process_id=>$meta_data){

						foreach($meta_data as $meta_key=>$meta_value){

							if(count($meta_value) > 1){
								$meta_value = json_encode($meta_value);
							}else{
								$meta_value = $meta_value[0];
							}

							$meta_entry = array(
								'entry_id'	 =>	$entryid,
								'process_id' => $process_id,
								'meta_key'	 =>	$meta_key,
								'meta_value' =>	$meta_value
							);
							$wpdb->insert($wpdb->prefix . 'cf_form_entry_meta', $meta_entry);

						}

					}
				}

				// return
				return;
			}

		}

	}

	/**
	 * Process mailer
	 *
	 * @since 1.2.3
	 *
	 * @param array $form Form config
	 * @param int|null $entryid Optional. ID of entry to send. If not provided, will be determined based on global $transdata
	 * @param array|null $data Optional. Data to use for sending. If not provided, will be retrieved by form ID.
	 * @param array|null $settings. Optional. If array then this is used for mail settings, not form mailier. Useful for auto-responder.
	 */
	public static function do_mailer( $form, $entryid = null, $data = null, array $settings = null ) {
		global $transdata, $processed_data;

		if( ! isset( $entryid ) && is_array( $processed_data ) ){
			if( isset( $processed_data[ $form['ID'] ], $processed_data[ $form['ID'] ][ '_entry_id' ] ) ){
				$entryid = $processed_data[ $form['ID'] ][ '_entry_id' ];
			}

		}

		if ( ! $data ) {
			$data = Caldera_Forms::get_submission_data($form, $entryid );
		}

		//fix for https://github.com/CalderaWP/Caldera-Forms/issues/888
		//Josh - Please find time to redo all CSV rendering please. Your buddy, Josh
		if( ! empty( $data ) ){
			foreach ( $data as $id => $datum ){
				$data[ $id ] = Caldera_Forms_Magic_Doer::maybe_implode_opts( $datum );
			}

		}

		// add entry ID to transient data
        //This should have already been set
        //See https://github.com/CalderaWP/Caldera-Forms/issues/2295#issuecomment-371325361
		$transdata['entry_id'] = $entryid;

		// do mailer!
		if ( empty( $settings ) ) {
			$sendername = __( 'Caldera Forms Notification', 'caldera-forms' );
			if ( ! empty( $form['mailer']['sender_name'] ) ) {
				$sendername = Caldera_Forms::do_magic_tags( $form['mailer']['sender_name'] );
				if ( false !== strpos( $sendername, '%' ) ) {
					$isname = Caldera_Forms::get_slug_data( trim( $sendername, '%' ), $form );
					if ( ! empty( $isname ) ) {
						$sendername = $isname;
					}
				}
			}
			if ( empty( $form['mailer']['sender_email'] ) ) {
				$sendermail = Caldera_Forms_Email_Fallback::get_fallback( $form );
			} else {
				$sendermail = Caldera_Forms::do_magic_tags( $form['mailer']['sender_email'] );
				if ( false !== strpos( $sendermail, '%' ) ) {
					$ismail = Caldera_Forms::get_slug_data( trim( $sendermail, '%' ), $form );
					if ( is_email( $ismail ) ) {
						$sendermail = $ismail;
					}
				}
			}
			// use summary
			if ( empty( $form['mailer']['email_message'] ) ) {
				$form['mailer']['email_message'] = "{summary}";
			}

			if ( ! isset( $form['mailer']['email_subject'] ) ) {
				$form['mailer']['email_subject'] = $form['name'];
			}


			$mail              = array(
				'recipients'  => array(),
				'subject'     => Caldera_Forms::do_magic_tags( $form['mailer']['email_subject'] ),
				'message'     => stripslashes( $form['mailer']['email_message'] ) . "\r\n",
				'headers'     => array(
					Caldera_Forms::do_magic_tags( 'From: ' . $sendername . ' <' . $sendermail . '>' )
				),
				'attachments' => array()
			);
			$mail['from']      = $sendermail;
			$mail['from_name'] = $sendername;


			// if added a bcc
			$mail['bcc'] = false;
			if ( isset( $form['mailer']['bcc_to'] ) && ! empty( $form['mailer']['bcc_to'] ) ) {
				$mail['bcc']       = $form['mailer']['bcc_to'];

				$bcc_array = array_map('trim', preg_split( '/[;,]/', Caldera_Forms::do_magic_tags( $form['mailer']['bcc_to'] ) ) );
				foreach( $bcc_array as $bcc_to ) {
					if ( is_email( $bcc_to ) ) {
						$mail['headers'][] = 'Bcc: ' . $bcc_to;
					}
				}
			}

			// if added a replyto
			$mail['replyto'] = false;
			if ( isset( $form['mailer']['reply_to'] ) ) {
                                $mail['replyto']   = $form['mailer']['reply_to'];
                                $mail['headers'][] = Caldera_Forms::do_magic_tags( 'Reply-To: ' . $form['mailer']['reply_to'] );
			}
			if ( ! $mail['replyto'] ) {
				$mail['replyto'] = $mail['from'];
			}

			// Filter Mailer first as not to have user input be filtered
			$mail['message'] = Caldera_Forms::do_magic_tags( $mail['message'], $entryid, $data );

			if ( ! isset( $form['mailer']['email_type'] ) || $form['mailer']['email_type'] == 'html' ) {
				$mail['headers'][] = "Content-type: text/html";
				$mail['html']      = true;
			} else {
				$mail['html'] = false;
			}

			// get tags
			preg_match_all( "/%(.+?)%/", $mail['message'], $hastags );
			if ( ! empty( $hastags[1] ) ) {
				foreach ( $hastags[1] as $tag_key => $tag ) {
					$tagval = Caldera_Forms::get_slug_data( $tag, $form );
					if ( is_array( $tagval ) ) {
						$tagval = implode( ', ', $tagval );
					}
					$mail['message'] = str_replace( $hastags[0][ $tag_key ], $tagval, $mail['message'] );
				}
			}


			// ifs
			preg_match_all( "/\[if (.+?)?\](?:(.+?)?\[\/if\])?/", $mail['message'], $hasifs );
			if ( ! empty( $hasifs[1] ) ) {
				// process ifs
				foreach ( $hasifs[0] as $if_key => $if_tag ) {

					$content = explode( '[else]', $hasifs[2][ $if_key ] );
					if ( empty( $content[1] ) ) {
						$content[1] = '';
					}
					$vars = shortcode_parse_atts( $hasifs[1][ $if_key ] );
					foreach ( $vars as $varkey => $varval ) {
						if ( is_string( $varkey ) ) {
							$var = Caldera_Forms::get_slug_data( $varkey, $form );
							if ( in_array( $varval, (array) $var ) ) {
								// yes show code
								$mail['message'] = str_replace( $hasifs[0][ $if_key ], $content[0], $mail['message'] );
							} else {
								// nope- no code
								$mail['message'] = str_replace( $hasifs[0][ $if_key ], $content[1], $mail['message'] );
							}
						} else {
							$var = Caldera_Forms::get_slug_data( $varval, $form );
							if ( ! empty( $var ) ) {
								// show code
								$mail['message'] = str_replace( $hasifs[0][ $if_key ], $content[0], $mail['message'] );
							} else {
								// no code
								$mail['message'] = str_replace( $hasifs[0][ $if_key ], $content[1], $mail['message'] );
							}
						}
					}
				}

			}


			if ( ! empty( $form['mailer']['recipients'] ) ) {
				$recipients_array = array_map('trim', preg_split( '/[;,]/', Caldera_Forms::do_magic_tags( $form['mailer']['recipients'] ) ) );
				foreach( $recipients_array as $recipient ) {
					if ( is_email( $recipient ) ) {
						$mail['recipients'][] = $recipient;
					}
				}
			} else {
				$mail['recipients'][] = Caldera_Forms_Email_Fallback::get_fallback( $form );
			}

			$csv_data = array();
			foreach ( $data as $field_id => $row ) {

				if ( $row === null || ! isset( $form['fields'][ $field_id ] ) ) {
					continue;
				}

				$key = $form['fields'][ $field_id ]['slug'];
				if( Caldera_Forms_Field_Util::is_file_field( $field_id, $form ) && Caldera_Forms_Files::is_private( Caldera_Forms_Field_Util::get_field( $field_id, $form ) ) ){
					continue;
				}

				if ( is_array( $row ) ) {
					if ( ! empty( $row ) ) {
						$keys = array_keys( $row );
						if ( is_int( $keys[0] ) ) {
							$row = implode( ', ', $row );
						} else {
							$tmp = array();
							foreach ( $row as $linekey => $item ) {
								if ( is_array( $item ) ) {
									$item = '( ' . implode( ', ', $item ) . ' )';
								}
								$tmp[] = $linekey . ': ' . $item;
							}
							$row = implode( ', ', $tmp );
						}
					} else {
						$row = null;
					}
				}

				$tag =  '%' . $key . '%';
				$parsed = Caldera_Forms_Magic_Doer::do_field_magic( $tag, $entryid, $form );
				$mail['message'] = str_replace( $tag, $parsed, $mail['message'] );
				$mail['subject'] = str_replace( $tag, $parsed, $mail['subject'] );

				$csv_data[] = array(
					'label' => $form['fields'][ $field_id ]['label'],
					'data' => $row
				);



			}

			// final magic
			$mail['message'] = Caldera_Forms::do_magic_tags( $mail['message'], $entryid, $form );
			$mail['subject'] = Caldera_Forms::do_magic_tags( $mail['subject'], $entryid, $form );

			// CSV
			$mail['csv'] = $csvfile = false;
			if ( ! empty( $form['mailer']['csv_data'] ) ) {
				/**
				 * Modify label or values for CSV attatched to Caldera Forms email
				 *
				 * @since 1.5.3
				 *
				 * @param array $csv_data Has key for labels and key for data
				 * @param array $form Form config
				 */
				$csv_data = apply_filters( 'caldera_forms_email_csv_data', $csv_data, $form );
				$labels = wp_list_pluck( $csv_data , 'label' );
				$submission = wp_list_pluck( $csv_data, 'data' );
				ob_start();
				$df = fopen( "php://output", 'w' );
				$file_type = Caldera_Forms_CSV_Util::file_type( $form );
				if ( 'tsv' == $file_type ) {
					$delimiter = chr(9);
				} else {
					$delimiter = ',';
				}
				fputcsv( $df, $labels, $delimiter );
				fputcsv( $df, $submission, $delimiter );
				fclose( $df );
				$csv     = ob_get_clean();


				$csvfile = wp_upload_bits( uniqid() . '.' . $file_type, null, $csv );
				if ( isset( $csvfile['file'] ) && false == $csvfile['error'] && file_exists( $csvfile['file'] ) ) {
					$mail['attachments'][] = $csvfile[ 'file' ];
					$mail[ 'csv' ]           = $csvfile[ 'file' ];
				}else{
					$mail[ 'csv' ] = false;
				}
			}
		} else {
			$mail = $settings;
			$csvfile = $settings[ 'csv' ];
		}

		if( empty( $mail ) ){
			/**
			 * Runs if mail was not sent because mail variable is empty.
			 * 
			 * If this fires, that is bad.
			 * 
			 * @since 1.4.0
			 * 
			 * @param array $form Form config
			 * @param int|null $entryid Optional. ID of entry to send. If not provided, will be determined based on global $transdata
			 * @param array|null $data Optional. Data to use for sending. If not provided, will be retrieved by form ID.
			 */
			do_action( 'caldera_forms_mailer_invalid', $form, $entryid, $data );
			return;
			
		}

		/**
		 * Filter email data before sending
		 *
		 * @since 1.2.3 in this location.
		 * @since unknown in original location (Caldera_Forms::save_final_form)
		 *
		 * @param array $mail Email data
		 * @param array $data Form entry data
		 * @param array $form The form config
		 * @param int|null $entryid Entry ID @since 1.5.0.10
		 */
		$mail = apply_filters( 'caldera_forms_mailer', $mail, $data, $form, $entryid );
		if ( empty( $mail ) || ! is_array( $mail ) ) {
			return;

		}

		if( ! $mail['html']     ){
			$mail[ 'message' ] = strip_tags( $mail['message'] );
		}

		$headers = implode("\r\n", $mail['headers']);

		if( empty( $mail[ 'message' ] ) ){
			$mail[ 'message' ] = '  ';
		}

		/**
		 * Runs before mail is sent, but after data is prepared
		 *
		 * @since 1.2.3 in this location.
		 * @since unknown in original location (Caldera_Forms::save_final_form)
		 *
		 * @param array $mail Email data
		 * @param array $data Form entry data
		 * @param array $form The form config
		 */
		do_action( 'caldera_forms_do_mailer', $mail, $data, $form);

		// force message to string.
		if( is_array( $mail['message'] ) ){
			$mail['message'] = implode( "\n", $mail['message'] );
		}

		if( ! empty( $mail ) ){

			// is send debug enabled?
			if( !empty( $form['debug_mailer'] ) ){
				add_action( 'phpmailer_init', array( 'Caldera_Forms', 'debug_mail_send' ), 1000 );
			}

			$sent = wp_mail( (array) $mail['recipients'], $mail['subject'], stripslashes( $mail['message'] ), $headers, $mail['attachments'] );
			self::after_send_email( $form, $data, $sent, $csvfile, $mail, 'wpmail' );
		}else{
			if( $csvfile ){
				self::unlink_csv( $csvfile );
			}


		}

	}

	/**
	 * Runs post email sent/not sent tasks/hooks etc
	 *
	 *
	 * @since 1.4.0
	 *
	 * @param array $form Form config
	 * @param array $data Submission data
	 * @param bool $sent Was email sent
	 * @param array|bool $csvfile Csv file array or false if not created
	 * @param array $mail The email
	 * @param string $method Method for sending email
	 */
	public static function after_send_email( $form, $data, $sent, $csvfile, $mail, $method ) {
		if ( $sent ) {

			if( $csvfile ){
				self::unlink_csv( $csvfile );
			}

			/**
			 * Fires main mailer completes
			 *
			 * @since 1.3.1
			 *
			 * @param array $mail Email data
			 * @param array $data Form entry data
			 * @param array $form The form config
			 * @param string $method Method for sending email
			 */
			do_action( 'caldera_forms_mailer_complete', $mail, $data, $form, $method );
		} else {
			/**
			 * Fires main mailer fails
			 *
			 * @since 1.2.3
			 *
			 * @param array $mail Email data
			 * @param array $data Form entry data
			 * @param array $form The form config
			 * @param string $method Method for sending email
			 */
			do_action( 'caldera_forms_mailer_failed', $mail, $data, $form, $method );

		}
	}

	/**
	 * Save an entry in the database
	 *
	 * IMPORTANT: Data is assumed to be sanatized, saving is assumed to be authorized. Do not hook directly to an HTTP request.
	 *
	 * @since 1.4.0
	 *
	 * @param array $form Form config
	 * @param array $fields Fields to save, must be in form of field_id => value and field IDs must exist
	 * @param array $args {
	 *     An array of arguments. As of 1.4.0 used for ovveriding entry status/user/time
	 *
	 *     @type string|int $user_id Optional. User ID. Default is current user ID.
	 *     @type string $datestamp Optional. Datestamp to use for entry. Must be mysql time. Default is current time.
	 *     @type string $status Optional. Status to use for entry. Must be a valid status. Default is 'active'.
	 * }
	 *
	 * @return array|int|string
	 */
	public static function create_entry( array $form, array $fields, array $args = array() ){
		$args = wp_parse_args( $args, array(
			'user_id' => get_current_user_id(),
			'datestamp' => current_time( 'mysql' ),
			'status' => 'active',

		) );
		if( isset( $form[ 'fields' ] ) ){
			$_fields = array();

			foreach( $fields as $field_id => $value ){
				if( array_key_exists($field_id, $form[ 'fields' ] ) ){
					$field = new Caldera_Forms_Entry_Field();
					$field->value = $value;
					$field->slug = $form[ 'fields' ][ $field_id ][ 'slug' ];
					$field->field_id = $field_id;
					$_fields[] = $field;
				}
			}

			if( ! empty( $_fields ) ){
				$_entry = new Caldera_Forms_Entry_Entry;
				$_entry->status = $args[ 'status' ];
				$_entry->form_id = $form[ 'ID' ];
				$_entry->datestamp = $args[ 'datestamp' ];
				$_entry->user_id = $args[ 'user_id' ];
				$entry = new Caldera_Forms_Entry( $form, false, $_entry );
				foreach( $_fields as $field ){
					$entry->add_field( $field );
				}

				return $entry->save();
			}

		}
		
	}

	/**
	 * After sending email, unlink CSV
	 *
	 * @since 1.4.0
	 *
	 * @param $csvfile
	 */
	protected static function unlink_csv( $csvfile ) {
		if ( is_array( $csvfile ) && ! empty( $csvfile[ 'file' ] ) ) {
			if ( file_exists( $csvfile[ 'file' ] ) ) {
				unlink( $csvfile[ 'file' ] );
			}

		}

	}


}
