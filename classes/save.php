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

	public static function determine_type( $form, $_cf_frm_edt ) {

	}

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
	 * @param int|null $entryid Optional. ID of entry to send. If not provided, will be deteremined based on global $transdata
	 * @param array|null $data Optional. Data to use for sending. If not provided, will be retrieved by form ID.
	 */
	public static function do_mailer( $form, $entryid = null, $data = null ) {
		global $transdata;

		if ( ! $data ) {
			$data = Caldera_Forms::get_submission_data($form);
		}

		// add entry ID to transient data
		if( ! isset( $entryid ) ){
			$entryid = null;
		}
		$transdata['entry_id'] = $entryid;

		// do mailer!
		$sendername = __('Caldera Forms Notification', 'caldera-forms');
		if(!empty($form['mailer']['sender_name'])){
			$sendername = $form['mailer']['sender_name'];
			if( false !== strpos($sendername, '%')){
				$isname = Caldera_Forms::get_slug_data( trim($sendername, '%'), $form);
				if(!empty( $isname )){
					$sendername = $isname;
				}
			}
		}
		if(empty($form['mailer']['sender_email'])){
			$sendermail = get_option( 'admin_email' );
		}else{
			$sendermail = $form['mailer']['sender_email'];
			if( false !== strpos($sendermail, '%')){
				$ismail = Caldera_Forms::get_slug_data( trim($sendermail, '%'), $form);
				if(is_email( $ismail )){
					$sendermail = $ismail;
				}
			}
		}
		// use summary
		if(empty($form['mailer']['email_message'])){
			$form['mailer']['email_message'] = "{summary}";
		}

		$mail = array(
			'recipients' => array(),
			'subject'	=> Caldera_Forms::do_magic_tags($form['mailer']['email_subject']),
			'message'	=> stripslashes( $form['mailer']['email_message'] ) ."\r\n",
			'headers'	=>	array(
				Caldera_Forms::do_magic_tags( 'From: ' . $sendername . ' <' . $sendermail . '>' ),
			),
			'attachments' => array()
		);

		// if added a bcc
		$bcc_to = trim( $form['mailer']['bcc_to'] );
		if( !empty( $bcc_to ) ){
			$mail['headers'][] = Caldera_Forms::do_magic_tags( 'Bcc: ' . $bcc_to );
		}

		// if added a replyto
		$reply_to = trim( $form['mailer']['reply_to'] );
		if( !empty( $reply_to ) ){
			$mail['headers'][] = Caldera_Forms::do_magic_tags( 'Reply-To: <' . $reply_to . '>' );
		}

		// Filter Mailer first as not to have user input be filtered
		$mail['message'] = Caldera_Forms::do_magic_tags($mail['message']);

		if($form['mailer']['email_type'] == 'html'){
			$mail['headers'][] = "Content-type: text/html";
			$mail['message'] = wpautop( $mail['message'] );
		}

		// get tags
		preg_match_all("/%(.+?)%/", $mail['message'], $hastags);
		if(!empty($hastags[1])){
			foreach($hastags[1] as $tag_key=>$tag){
				$tagval = Caldera_Forms::get_slug_data($tag, $form);
				if(is_array($tagval)){
					$tagval = implode(', ', $tagval);
				}
				$mail['message'] = str_replace($hastags[0][$tag_key], $tagval, $mail['message']);
			}
		}

		//$mail['message']

		// ifs
		preg_match_all("/\[if (.+?)?\](?:(.+?)?\[\/if\])?/", $mail['message'], $hasifs);
		if(!empty($hasifs[1])){
			// process ifs
			foreach($hasifs[0] as $if_key=>$if_tag){

				$content = explode('[else]', $hasifs[2][$if_key]);
				if(empty($content[1])){
					$content[1] = '';
				}
				$vars = shortcode_parse_atts( $hasifs[1][$if_key]);
				foreach($vars as $varkey=>$varval){
					if(is_string($varkey)){
						$var = Caldera_Forms::get_slug_data($varkey, $form);
						if( in_array($varval, (array) $var) ){
							// yes show code
							$mail['message'] = str_replace( $hasifs[0][$if_key], $content[0], $mail['message']);
						}else{
							// nope- no code
							$mail['message'] = str_replace( $hasifs[0][$if_key], $content[1], $mail['message']);
						}
					}else{
						$var = Caldera_Forms::get_slug_data($varval, $form);
						if(!empty($var)){
							// show code
							$mail['message'] = str_replace( $hasifs[0][$if_key], $content[0], $mail['message']);
						}else{
							// no code
							$mail['message'] = str_replace( $hasifs[0][$if_key], $content[1], $mail['message']);
						}
					}
				}
			}

		}


		if(!empty($form['mailer']['recipients'])){
			$mail['recipients'] = explode(',', Caldera_Forms::do_magic_tags( $form['mailer']['recipients']) );
		}else{
			$mail['recipients'][] = get_option( 'admin_email' );
		}

		$submission = array();
		foreach ($data as $field_id=>$row) {
			if($row === null || !isset($form['fields'][$field_id]) ){
				continue;
			}

			$key = $form['fields'][$field_id]['slug'];
			if(is_array($row)){
				if(!empty($row)){
					$keys = array_keys($row);
					if(is_int($keys[0])){
						$row = implode(', ', $row);
					}else{
						$tmp = array();
						foreach($row as $linekey=>$item){
							if(is_array($item)){
								$item = '( ' . implode(', ', $item).' )';
							}
							$tmp[] = $linekey.': '.$item;
						}
						$row = implode(', ', $tmp);
					}
				}else{
					$row = null;
				}
			}
			$mail['message'] = str_replace('%'.$key.'%', $row, $mail['message']);
			$mail['subject'] = str_replace('%'.$key.'%', $row, $mail['subject']);

			$submission[] = $row;
			$labels[] = $form['fields'][$field_id]['label'];
		}

		// final magic
		$mail['message'] = Caldera_Forms::do_magic_tags( $mail['message'] );
		$mail['subject'] = Caldera_Forms::do_magic_tags( $mail['subject'] );

		// CSV
		if(!empty($form['mailer']['csv_data'])){
			ob_start();
			$df = fopen("php://output", 'w');
			fputcsv($df, $labels);
			fputcsv($df, $submission);
			fclose($df);
			$csv = ob_get_clean();
			$csvfile = wp_upload_bits( uniqid().'.csv', null, $csv );
			if( isset( $csvfile['file'] ) && false == $csvfile['error'] && file_exists( $csvfile['file'] ) ){
				$mail['attachments'][] = $csvfile['file'];
			}
		}

		if(empty($mail)){
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
		 */
		$mail = apply_filters( 'caldera_forms_mailer', $mail, $data, $form);


		$headers = implode("\r\n", $mail['headers']);

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

			if( wp_mail( (array) $mail['recipients'], $mail['subject'], stripslashes( $mail['message'] ), $headers, $mail['attachments'] )){

				// kill attachment.
				if(!empty($csvfile['file'])){
					if(file_exists($csvfile['file'])){
						unlink($csvfile['file']);
					}
				}
				
				/**
				 * Fires main mailer completes
				 *
				 * @since 1.3.1
				 *
				 * @param array $mail Email data
				 * @param array $data Form entry data
				 * @param array $form The form config
				 */
				do_action( 'caldera_forms_mailer_complete', $mail, $data, $form );				
			}else{
				/**
				 * Fires main mailer fails
				 *
				 * @since 1.2.3
				 *
				 * @param array $mail Email data
				 * @param array $data Form entry data
				 * @param array $form The form config
				 */
				do_action( 'caldera_forms_mailer_failed', $mail, $data, $form );

			}
		}else{
			if(!empty($csvfile['file'])){
				if(file_exists($csvfile['file'])){
					unlink($csvfile['file']);
				}
			}

		}

	}

}
