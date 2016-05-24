<?php

/**
 * Entry values database abstraction
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_DB_Entry_Values extends Caldera_Forms_DB_Base {

	/**
	 * Primary fields
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $primary_fields = array(
		'id'    => array(
			'%d',
			'absint'
		),
		'entry_id' => array(
			'%d',
			'absint'
		),
		'field_id'    => array(
			'%s',
			'strip_tags'
		),
		'slug'    => array(
			'%s',
			'caldera_forms_very_safe_string'
		),
		'value'    => array(
			'%s',
			'caldera_forms_very_safe_string'
		),

	);

	/**
	 * Meta fields
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $meta_fields = array(
		'meta_id'    => array(
			'%d',
			'absint'
		),
		'entry_id' => array(
			'%d',
			'absint'
		),
		'process_id'    => array(
			'%s',
			'strip_tags'
		),
		'meta_key'    => array(
			'%s',
			'strip_tags'
		),
		'meta_value'    => array(
			'%s',
			'caldera_forms_sanitize'
		)
	);

	/**
	 * Meta keys
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $meta_keys = array();

	/**
	 * Name of primary index
	 *
	 * @since 1.3.6
	 *
	 * @var string
	 */
	protected $index = 'id';

	/**
	 * Name of table
	 *
	 * @since 1.3.6
	 *
	 * @var string
	 */
	protected $table_name = 'cf_form_entry_values';


	/**
	 * Get name of table with prefix
	 *
	 * @since 1.3.6
	 *
	 * @param bool|false $meta Whether primary or meta table name is desired. Default is false, which returns primary table
	 *
	 * @return string
	 */
	public function get_table_name( $meta = false ){
		global $wpdb;

		$table_name = $wpdb->prefix . $this->table_name;
		if( $meta ){
			$table_name = $wpdb->prefix . 'cf_form_entry_meta';
		}

		return $table_name;
	}
}
