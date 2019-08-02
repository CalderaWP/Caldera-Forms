<?php

/**
 * Handles file uploading from file fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 CalderaWP LLC
 */
class Caldera_Forms_Files{

    const CRON_ACTION = 'caldera_forms_delete_files';

    /**
     * Holds upload dir path for non-media library uploads
     *
     * @since 1.4.4
     *
     * @var string
     */
    protected static $dir;

    /**
     * Upload a file to WordPress
     *
     * @since 1.4.4
     *
     * @param array $file File
     * @param array $args Optional. Used to place in private dir
     *
     * @return array
     */
    public static function upload( $file, array  $args = array() ){
        $args = wp_parse_args($args, array(
            'private' => false,
            'field_id' => null,
            'form_id' => null,
			'transient_id' => null,
        ));
        if( true == $args[ 'private' ] && ! empty( $args[ 'field_id' ] ) && ! empty( $args[ 'form_id' ] )){
            $private = true;
        }else{
            $private = false;
        }


	    self::add_upload_filter( $args[ 'field_id' ],  $args[ 'form_id' ], $private, $args[ 'transient_id' ] );

        $upload = wp_handle_upload($file, array( 'test_form' => false, 'foo' => 'bnar' ) );

        if( $private ){
            self::remove_upload_filter();
        }

		self::schedule_delete($args, $private, $upload);

		return $upload;

    }

    /**
     * Add uploaded file to media library
     *
     * @since 1.4.4
     *
     * @param array $upload Uploaded file data
     * @param array $field Optional. Field config for file field doing upload. @since 1.5.1
     *
     * @return int|string|bool The ID of attachment or false if error @since 1.5.0.8
     */
    public static function add_to_media_library( $upload, $field ){
    	if( isset( $upload[ 'error' ] ) ){
    		return false;
	    }

        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        $media_item = array(
            'guid'           => $upload['file'],
            'post_mime_type' => $upload['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $media_id = wp_insert_attachment( $media_item, $upload['file'] );

        $media_data = wp_generate_attachment_metadata( $media_id, $upload['file'] );
        wp_update_attachment_metadata( $media_id, $media_data );

	    /**
	     * Runs after file is uploaded to media library by Caldera Forms
	     *
	     * @since 1.5.1
	     *
	     * @param int|bool Attachment ID or false if upload failed
	     * @param array $field Field config
	     */
		do_action( 'caldera_forms_file_added_to_media_library', $media_id, $field );
	    return $media_id;
    }

    /**
     * Setup upload directory filter
     *
     * @since 1.4.4
     *
     * @param string $field_id The field ID for file field
     * @param string $form_id The form ID
	 * @param bool $private If is private
	 * @param null|string $transient_id ID of transient for file. Optional.
     */
    public static function add_upload_filter( $field_id , $form_id, $private = true, $transient_id = null ){
	    if ( $private ) {
		    self::$dir = apply_filters( 'caldera_forms_private_upload_directory', self::secret_dir( $field_id, $form_id, $transient_id), $field_id, $form_id, $transient_id );
	    }else{
		    /**
		     * Filter directory for uploaded files
		     *
		     * If null, the file path will be WordPress' default
		     *
		     * @since 1.4.5
		     *
		     * @param string|null Directory
		     * @param string $field_id Field ID
		     * @param string $form_id Form ID
			 */
	    	$dir = apply_filters( 'caldera_forms_upload_directory', null, $field_id, $form_id );
		    if( null != $dir ){
		    	self::$dir = $dir;
		    }
	    }
        add_filter( 'upload_dir', array( __CLASS__, 'uploads_filter' ) );
    }

    /**
     * Remove the filter for upload directory path
     *
     * @since 1.4.4
     */
    public static function remove_upload_filter(){
        remove_filter( 'upload_dir', array( __CLASS__, 'uploads_filter' ) );
    }

    /**
     * Filter upload directory
     *
     * @uses "upload_dir" filter
     *
     * @since 1.4.4
     *
     * @param array $args
     * @return array
     */
    public static function uploads_filter( $args ){

	    if (  self::$dir ) {
		    $newdir = '/' . self::$dir;

		    $args[ 'path' ]   = str_replace( $args[ 'subdir' ], '', $args[ 'path' ] );
		    $args[ 'url' ]    = str_replace( $args[ 'subdir' ], '', $args[ 'url' ] );
		    $args[ 'subdir' ] = $newdir;
		    $args[ 'path' ] .= $newdir;
		    $args[ 'url' ] .= $newdir;
		    if( ! file_exists( $args[ 'path' ] ) ){
		    	$created = wp_mkdir_p( $args[ 'path' ] );
		    }
	    }

        return $args;
    }

    /**
     * Get a secret file fir by field ID and form ID
     *
     * @since 1.4.4
     *
     * @param string $field_id The field ID for file field
     * @param string $form_id The form ID
	 * @param null|string $transient_id ID of transient for file. Optional.
	 *
	 * @return string
     */
    protected static function secret_dir( $field_id, $form_id, $transient_id = null ){
        return md5( $field_id . $form_id . NONCE_SALT . (string) $transient_id );

    }

    /**
     * Delete all files from the secret dir for a field
     *
     * @since 1.4.4
     *
     * @param string $field_id The field ID for file field
     * @param string $form_id The form ID
     */
    protected static function delete_uploaded_files( $field_id, $form_id ){
		$uploads = wp_get_upload_dir();
        $dir = $uploads[ 'basedir' ] . '/' . self::secret_dir($field_id, $form_id);
        if (is_dir($dir)) {
            array_map('unlink', glob($dir . '/*'));
            rmdir($dir);
        }

    }

    /**
     * After form submit, clear out files from secret dirs
     *
     * @since 1.4.4
     *
     * @param array $form Form config
     * @param bool $second_run Optional. If using at mail hooks, set true to prevent recurrsion
     */
    public static function cleanup( $form, $second_run = false ){
        if( false === $second_run && Caldera_Forms::should_send_mail( $form ) ) {
            add_action( 'caldera_forms_mailer_complete', array( __CLASS__, 'delete_after_mail' ), 10, 3 );
            add_action( 'caldera_forms_mailer_failed', array( __CLASS__, 'delete_after_mail' ), 10, 3 );
            return;
        }

        $form_id = $form[ 'ID' ];
        $fields = Caldera_Forms_Forms::get_fields( $form, false );
        foreach( $fields as $id => $field ){
            if( Caldera_Forms_Field_Util::is_file_field( $field, $form ) ){
                self::delete_uploaded_files( $field[ 'ID' ], $form_id );
            }

        }

    }

    /**
     * Do cleanup after sending email
     *
     * We use "caldera_forms_submit_complete" to start the clean up, but that is too soon, if using mailer.
     *
     * @since 1.4.4
     *
     * @param $mail
     * @param $data
     * @param $form
     */
    public static function delete_after_mail( $mail, $data, $form ){
        self::cleanup( $form, true );
    }

    /**
     * Trigger file delete via CRON
     *
     * This is needed because if a form never completed submission, files are not deleted at caldera_forms_submit_complete
     *
     * @since 1.4.4
     *
     * @param array $args
     */
    public static function cleanup_via_cron( $args ){
        if( isset( $args[0], $args[1] ) ){
            self::delete_uploaded_files( $args[0], $args[1] );
        }

    }

	/**
	 * Check if file field's uploads are private or not
	 *
	 * @since 1.4.4
	 *
	 * @param array $field Field config
	 *
	 * @return bool
	 */
    public static function is_private( array  $field ){

    	return Caldera_Forms_Field_Util::is_file_field( $field ) && ! self::should_add_to_media_library($field);
    }


	/**
	 * Check if a file field is "persistent"
	 *
	 * If true, file should be retained on server.
	 *
	 * @since 1.8.0
	 *
	 * @param array $field Field config
	 *
	 * @return bool
	 */
	public static function is_persistent(array $field ){
		return self::should_add_to_media_library($field) || ! empty( $field['config']['persistent']);
	}

	/**
	 * Get max size for file field
	 *
	 * @since 1.8.0
	 *
	 * @param array $field Field config
	 *
	 * @return int
	 */
	public static function get_max_upload_size( array $field)
	{
		return ! empty( $field[ 'config']['max_upload'] ) ? absint( $field[ 'config']['max_upload']) : 0;
	}

	/**
	 * Check if a file is too large to upload
	 *
	 * @since 1.8.0
	 *
	 * Returns false if no max upload size set.
	 * Returns true if max upload size option is set and file is larger than limit
	 *
	 * @param array $field Field config
	 * @param string $file File to check size against
	 *
	 * @return bool
	 */
	public static function is_file_too_large( array  $field, $file )
	{

		return 0 !== self::get_max_upload_size($field) && self::get_max_upload_size($field) < $file['size'];

	}
	/**
	 * Check if a file field should upload to media library.
	 **
	 *
	 * @since 1.8.0
	 *
	 * @param array $field Field config
	 *
	 * @return bool
	 */
	public static function should_add_to_media_library(array $field ){
		return isset( $field[ 'config']['media_lib'] ) && true == $field['config']['media_lib'];
	}

	/**
	 * Get the callback function for file uploads
	 *
	 * @since 1.4.4
	 *
	 * @param array $form Form config
	 * @param array $field Field config
	 *
	 * @return array|string|callable
	 */
    public static function get_upload_handler( $form, $field ){

    	/**
	     * Filter the callback function for file uploads
	     *
	     * @since 1.4.4
	     *
	     * @param array|string|callable Callable
	     * @param array $form Form config
	     * @param array $field Field config
	     */
    	return apply_filters( 'caldera_forms_file_upload_handler', array( 'Caldera_Forms_Files', 'upload' ), $form, $field );
    }

	/**
	 * Check if field's files should be attached
	 *
	 * @since 1.5.0
	 *
	 * @param array $field
	 * @param array $form Form config
	 * @return bool
	 */
    public static function should_attach( array  $field, array $form ){

    	if( Caldera_Forms_Field_Util::is_file_field( $field, $form ) ){
		    return ! empty( $field[ 'config' ][ 'attach'] );
	    }

	    return false;

    }

	/**
	 * Get types of file fields
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
    public static function types(){
    	return array(
    	    'advanced_file',
            'file',
            \calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType::getCf1Identifier(),
            \calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType::getType()
        );
    }

	/**
	 * Schedule file to be deleted as soon as possible
	 *
	 * @since 1.8.0
	 *
	 * @param string $field_id ID of field
	 * @param string $form_id ID of form
	 * @param string $file Path to file to delete.
	 *
	 * @return bool
	 */
	public static function schedule_delete($field_id, $form_id, $file )
	{
		$form = Caldera_Forms_Forms::get_form($form_id);
		if ( is_array($form) ) {
			$field = Caldera_Forms_Field_Util::get_field($field_id, $form);
			if ( is_array($field) && !self::is_persistent($field) ) {
				caldera_forms_schedule_job(new \calderawp\calderaforms\cf2\Jobs\DeleteFileJob($file));
				return true;
			}
		}

		return false;

	}

}
