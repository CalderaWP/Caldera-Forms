<?php


namespace calderawp\calderaforms\pro\settings;


/**
 * Class active
 * @package calderawp\calderaforms\pro\settings
 */
class active {

	/**
	 * Check if CF Pro API is active for this site or not
	 *
	 * @since 0.2.0
	 *
	 * @return bool
	 */
	public static function get_status(){
		/**
		 * Override active status
		 *
		 * @since 0.2.0
		 *
		 * @param bool $status
		 */
		return (bool) apply_filters( 'caldera_forms_pro_is_active', true  );
	}

}