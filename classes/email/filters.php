<?php

/**
 * Email formatting filters
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2018 CalderaWP LLC
 */
class Caldera_Forms_Email_Filters{

    /**
     * Prepare email attachments
     *
     * @uses "caldera_forms_mailer"
     *
     * @since 1.5.9
     *
     * @param array $mail Email data
     * @param array $data Form data
     * @param array $form For config
     *
     * @return array
     */
    public static function mail_attachment_check( $mail, $data, $form){
        foreach ( Caldera_Forms_Forms::get_fields( $form, false ) as $field_id => $field ) {
            if ( Caldera_Forms_Field_Util::is_file_field( $field, $form )  ) {
                //Filter field config before checking if should attach
                if( ! Caldera_Forms_Files::should_attach( \Caldera_Forms_Field_Util::get_field($field_id,$form,true), $form ) ){
                    continue;
                }
                $dir = wp_upload_dir();
                if ( isset( $data[ $field_id ] ) && is_array( $data[ $field_id ] ) ) {
                    foreach ( $data[ $field_id ] as $file ) {
                        $file = str_replace( $dir[ 'baseurl' ], $dir[ 'basedir' ], $file );
                        if ( file_exists( $file ) ) {
                            $mail[ 'attachments' ][] = $file;
                        }
                    }
                    continue;
                }

                if( Caldera_Forms_Field_Util::is_cf2_field_type(Caldera_Forms_Field_Util::get_type($field,$form))){
					$file = Caldera_Forms::get_field_data( $field_id, $form );
					if ( is_array( $file ) ) {
						foreach ( $file as $a_file ) {
							$a_file = str_replace( $dir[ 'baseurl' ], $dir[ 'basedir' ], $a_file );
							if ( is_string( $a_file ) && file_exists( $a_file ) ) {
								$mail[ 'attachments' ][] = $a_file;
							}
						}
						continue;
					}
				}else{
					if ( isset( $data[ $field_id ] ) ) {
						$file = $data[ $field_id ];
					} else {
						$file = Caldera_Forms::get_field_data( $field_id, $form );
					}
					if ( is_array( $file ) ) {
						foreach ( $file as $a_file ) {
							$file = str_replace( $dir[ 'baseurl' ], $dir[ 'basedir' ], $file );
							if ( is_string( $a_file ) && file_exists( $a_file ) ) {
								$mail[ 'attachments' ][] = $a_file;
							}
						}
						continue;
					} else {
						$file = str_replace( $dir[ 'baseurl' ], $dir[ 'basedir' ], $file );
						if ( is_string( $file ) ) {
							$files = explode(',', $file);
							foreach($files as $attachment){
								$attachment = ltrim( $attachment, " ");
								if( file_exists($attachment) ){
									$mail[ 'attachments' ][] = $attachment;
								}
							}
						} else {
							if ( isset( $data[ $field_id ] ) && filter_var( $data[ $field_id ], FILTER_VALIDATE_URL ) ) {
								$mail[ 'attachments' ][] = $data[ $field_id ];
							} elseif ( isset( $_POST[ $field_id ] ) && filter_var( $_POST[ $field_id ], FILTER_VALIDATE_URL ) && 0 === strpos( $_POST[ $field_id ], $dir[ 'url' ] ) ) {
								$mail[ 'attachments' ][] = $_POST[ $field_id ];
							} else {
								continue;
							}
						}
					}
				}
				}

        }
        return $mail;
    }

    /**
     * Apply wpautop to autoresponder message.
     *
     * This was separated out from main autoresponder generation method in 1.5.9 so it would be removable, see: https://github.com/CalderaWP/Caldera-Forms/issues/1917
     *
     * @since 1.5.9
     *
     * @uses "caldera_forms_autoresponse_mail" filter
     *
     * @param array $email
     *
     * @return mixed
     */
    public static function format_autoresponse_message( $email ) {
        $email[ 'message' ] = wpautop( $email[ 'message' ] );
        return $email;

    }

    /**
     * Apply wpautop to email message.
     *
     * This was separated out from main email generation method in 1.4.7 so it would be removable, see: https://github.com/CalderaWP/Caldera-Forms/issues/1048
     *
     * @since 1.5.9
     *
     * @uses "caldera_forms_mailer" filter
     *
     * @param array $mail
     *
     * @return mixed
     */
    public static function format_message( $mail ){
        $mail[ 'message' ] = wpautop( $mail[ 'message' ] );
        return $mail;
    }

    /**
     * Prepare email headers
     *
     * @since 1.5.9
     *
     *
     * @uses "caldera_forms_mailer" filter
     * @uses "caldera_forms_autoresponse_mail" filter
     *
     * @param array $mail
     * @return array
     */
    public static function prepare_headers($mail){
        if( ! empty($mail['recipients'])){
            if( is_array( $mail['recipients'])){
                foreach( $mail['recipients'] as &$recipient){
                    $recipient = Caldera_Forms_Sanitize::sanitize_header($recipient);
                }
            } else{
                $mail['recipients']= Caldera_Forms_Sanitize::sanitize_header($mail['recipients']);
            }
        }

        if( ! empty($mail['headers'])){
            if( is_array( $mail['headers'])){
                foreach( $mail['headers'] as &$header){
                    $header = Caldera_Forms_Sanitize::sanitize_header($header);
                }
            } else{
                $mail['headers']= Caldera_Forms_Sanitize::sanitize_header($mail['headers']);
            }
        }
        return $mail;
    }

}
