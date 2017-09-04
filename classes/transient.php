<?php

/**
 * Class Caldera_Forms_Transient
 *
 * Abstraction for backing global $transdata in DB and other data needed to be shared between sessions.
 *
 * History (A bold march towards a proper session API)
 * Before 1.4.9.1 get/set_transient was used as a convention.
 * In 1.4.9.1 This class was added as an API wrapping get/set_transient()
 * In 1.5.6 This API switched to using get/update_option() with WordPress "CRON job" to delete if needed, which it should not be most of the time, transient should be deleted on caldera_forms_submit_complete aaction. Reasons explained in https://github.com/CalderaWP/Caldera-Forms/issues/1866
 */
class Caldera_Forms_Transient {

	/**
	 * Tracks the transients to be deleted at caldera_forms_submit_complete action
	 *
	 * @since 1.5.6
	 *
	 * @var array
	 */
	protected static $delete_at_submission_complete;

	/**
	 * Hookname for wp-cron single events used to delete our "transients"
	 */
	const CRON_ACTION = 'caldera_forms_transient_delete';

	/**
	 * Set a transient
	 *
	 * @since 1.4.9.1
	 *
	 * @param string $id Transient ID
	 *
	 * @return mixed
	 */
	public static function get_transient( $id ){
		return get_option( self::prefix( $id ) );
	}

	/**
	 * Get stored transient
	 *
	 * @since 1.4.9.1
	 *
	 * @param string $id Transient ID
	 * @param mixed $data Data
	 * @param null|int $expires Optional. Expiration time. Default is nul, which becomes 1 hour
	 *
	 * @return bool
	 */
	public static function set_transient( $id, $data, $expires = null ){
		if( ! is_numeric( $expires ) &&  isset( $data[ 'expires' ] ) && is_numeric( $data[ 'expires' ] ) ){
			$expires = $data[ 'expires' ];
		}elseif ( ! is_numeric( $expires ) ){
			$expires = HOUR_IN_SECONDS;
		}else{
			$expires = absint( $expires );
		}

		wp_schedule_single_event( time() + $expires, self::CRON_ACTION, array(  $id  ) );
		return update_option( self::prefix( $id ), $data, false );

	}

	/**
	 * Delete transient
	 *
	 * @since 1.5.0.7
	 *
	 * @param string $id Transient ID
	 *
	 * @return bool
	 */
	public static function delete_transient( $id ){
		return delete_option( self::prefix( $id ) );
	}

	/**
	 * Create transient prefix
	 *
	 * @since 1.5.0.7
	 *
	 * @param string $id Transient ID
	 *
	 * @return string
	 */
	protected static function prefix( $id ){
		return 'cftransdata_' . $id;
	}

	/**
	 * Callback function for "transients" being deleted via CRON
	 *
	 * @since 1.5.6
	 *
	 * @param $args
	 */
	public static function cron_callback( $args ){
		if( isset( $args[0] ) ){
			self::delete_transient( $args[0] );
		}

	}

	/**
	 * Add a transient to be deleted at submission completed action
	 *
	 * @since 1.5.5
	 *
	 * @param string $id Transient ID -- not prefixed
	 */
	public static function delete_at_submission_complete( $id ){
		self::$delete_at_submission_complete[] = $id ;
	}

	/**
	 * Clear any "transients" marked to be cleared when submission completes
	 *
	 * @since 1.5.5
	 *
	 * @uses "caldera_forms_submit_complete"
	 */
	public static function submission_complete(){
		if( ! empty( self::$delete_at_submission_complete )  ){
			foreach ( self::$delete_at_submission_complete as $id  ) {
				self::delete_transient( $id );
			}

		}

	}
}