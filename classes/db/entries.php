<?php

/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class Caldera_Forms_DB_Entries extends Caldera_Forms_DB_Base {

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
		'form_id' => array(
			'%s',
			'strip_tags'
		),
		'user_id'    => array(
			'%d',
			'absint'
		),
		'datestamp' => array(
			'%s',
			'strip_tags'
		),
		'status' => array(
			'%s',
			'strip_tags'
		),
	);

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
	 * @since 1.3.5
	 *
	 * @var string
	 */
	protected $table_name = 'cf_form_entries';


	/**
	 * There is no meta here, so always returns null.
	 *
	 * @since 1.3.6
	 *
	 * @param int|array $id Don't use
	 * @param string|bool $key Don't use
	 *
	 * @return array|null|object
	 */
	public function get_meta( $id, $key = false ){
		return null;
	}
}
