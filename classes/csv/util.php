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
		 * @param array $form Current form configuration. Might be empty
		 */
		return apply_filters( 'caldera_forms_csv_character_encoding', 'utf-8', $form );
	}

	/**
	 * Check if CSV export should show time in localized or UTC time.
	 *
	 * @since 1.5.6
	 *
	 * @param array $form
	 *
	 * @return bool
	 */
	public static function should_localize_time( array  $form = array() ){
		/**
		 * Change if localized or UTC timestamp should be used in CSV export
		 *
		 * @since 1.5.6
		 *
		 * @param bool $localize. Default is false. Change to true to use localized time, instead of UTC.
		 * @param array $form Current form configuration. Might be empty
		 */
		return apply_filters( 'caldera_forms_csv_localize_time', false, $form );
	}

	/**
	 * Get the file type for CSV -- tsv or csv
	 *
	 * @since 1.5.6
	 *
	 * @param array $form Form config. Technically optional, but should always be passed for benefit of filter.
	 *
	 * @return string
	 */
	public static function file_type( array  $form = array() ){

		/**
		 * Change the file type for CSV -- tsv or csv
		 *
		 * @since 1.5.6
		 *
		 * @param string $type. Default is 'csv'
		 * @param array $form Current form configuration. Might be empty
		 */
		return apply_filters( 'caldera_forms_csv_file_type', 'csv', $form );
	}
}
