<?php
/**
 * Updaters for DB structure/etc.
 *
 * @package   Caldera_Froms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

/**
 * Update Caldera Forms DB system to v2
 *
 * @since 1.3.4
 */
function caldera_forms_db_v2_update(){
	$forms = get_option( '_caldera_forms', array() );
	if( ! empty( $forms ) ){
		$where = '`option_name` = "' . implode( '" OR `option_name` = "', array_keys( $forms ) ) . '"';

		global $wpdb;
		$sql = sprintf( "UPDATE `%s` SET `autoload`='no' WHERE %s", $wpdb->options, $where );
		$wpdb->get_results( $sql  );

		$new_registry = array();
		if( ! empty( $forms ) ){
			foreach( $forms as $id => $form ){
				$new_registry[ $id ] = $id;
			}


		}

		add_option( '_caldera_forms_forms', $new_registry, false );

		caldera_forms_write_db_flag( 2 );
		
	}

}


/**
 * Write DB version flag to options
 *
 * @since 1.3.4
 *
 * @param int $version Optional. The version number to write. Default is value of CF_DB
 */
function caldera_forms_write_db_flag( $version = CF_DB ){
	update_option( 'CF_DB',  $version );
}

/**
 * Gets the last update version
 *
 * @since 1.5.0.9
 *
 * @return string
 */
function caldera_forms_get_last_update_version(){
	return get_option( '_calderaforms_lastupdate' );
}