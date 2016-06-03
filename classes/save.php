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
		global $transdata, $processed_data;

		if( ! isset( $entryid ) && is_array( $processed_data ) ){
			if( isset( $processed_data[ $form['ID'] ], $processed_data[ $form['ID'] ][ '_entry_id' ] ) ){
				$entryid = $processed_data[ $form['ID'] ][ '_entry_id' ];
			}

		}

		if ( ! $data ) {
			$data = Caldera_Forms::get_submission_data($form, $entryid );
		}

		// add entry ID to transient data

		$transdata['entry_id'] = $entryid;

		$mail_data = new Caldera_Forms_Mail_Data( $form, $entryid, $data );
		$mail = $mail_data->get_mail();

		/**
		 * Filter email data before sending
		 *
		 * @since 1.2.3 in this location.
		 * @since unknown in original location (Caldera_Forms::save_final_form)
		 *
		 * @param array $mail Email data
		 * @param array $data Form entry data
		 * @param array $form The form config
		 * @param Caldera_Forms_Mail_Data $mail_data Email data object @since 1.3.6
		 */
		$mail = apply_filters( 'caldera_forms_mailer', $mail, $data, $form, $mail_data );
		if ( empty( $mail ) || ! is_array( $mail ) ) {
			return;

		}

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
