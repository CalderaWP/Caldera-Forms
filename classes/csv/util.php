<?php

/**
 * Utility functions for CSV related work
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_CSV_Util {

	/**
	 * Get the character encoding for CSV files
	 *
	 * @since 1.4.4
	 *
	 * @param array $form Form config. Technically optional, but should always be passed for benefit of filter.
	 *
	 * @return string
	 */
	public static function character_encoding( array  $form = array() ){

		/**
		 * Change the character encoding for CSV
		 *
		 * Used for entry export and email attachments
		 *
		 * @since 1.4.4
		 *
		 * @param string $encoding. Default is 'utf-8'
		 * @param array $form Current form configuration.
		 */
		return apply_filters( 'caldera_forms_csv_character_encoding', 'utf-8', $form );
	}

}
