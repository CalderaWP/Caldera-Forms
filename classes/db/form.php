<?php


/**
 * Class Caldera_Forms_DB_Form
 */
class Caldera_Forms_DB_Form extends Caldera_Forms_DB_Base {

	/**
	 * Primary fields
	 *
	 * @since 1.5.3
	 *
	 * @var array
	 */
	protected $primary_fields = array(
		'form_id'    => array(
			'%s',
			'strip_tags'
		),
		'process_id' => array(
			'%s',
			'strip_tags'
		)
	);

}