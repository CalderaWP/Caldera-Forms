<?php
/**
 * Update entry values
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */

/**
 * Class Caldera_Forms_Entry_Update
 */
class Caldera_Forms_Entry_Update {

	/**
	 * Update an entry status
	 *
	 * @since 1.5.0
	 *
	 * @param string $new_status
	 * @param int|string $entry_id
	 *
	 * @return bool
	 */
	public static function update_entry_status(  $new_status, $entry_id ){
		global $wpdb;
		$updated = $wpdb->update($wpdb->prefix . 'cf_form_entries', array(
			'status' => caldera_forms_very_safe_string( $new_status ),
		), array(
			'id' => absint( $entry_id )
		) );
		if( false !== $updated ){
			return true;
		}

		return false;

	}
}