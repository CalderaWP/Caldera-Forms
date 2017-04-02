<?php

/**
 * Class Caldera_Forms_Transient
 */
class Caldera_Forms_Transient {

	protected static $prefix = 'cftransdata_';

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
		return get_transient( self::prefix( $id ) );
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

		return set_transient( self::prefix( $id ), $data, $expires );
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
		return delete_transient( self::prefix( $id ) );
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
		return self::$prefix . $id;
	}
}