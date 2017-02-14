<?php
/**
 * @TODO What this does
 *
 * @package cf
 * Copyright 2017 Josh Pollock <Josh@CalderaWP.com
 */

/**
 * Class Caldera_Forms_Transient
 */
class Caldera_Forms_Transient {

	protected static $prefix = 'cftransdata_';

	public static function get_transient( $id ){
		return get_transient( self::$prefix . $id );
	}

	public static function set_transient( $id, $data, $expires = null ){
		if( ! is_numeric( $expires ) &&  isset( $data[ 'expires' ] ) && is_numeric( $data[ 'expires' ] ) ){
			$expires = $data[ 'expires' ];
		}elseif ( ! is_numeric( $expires ) ){
			$expires = HOUR_IN_SECONDS;
		}else{
			$expires = absint( $expires );
		}

		return set_transient( self::$prefix . $id, $data, $expires );
	}

}