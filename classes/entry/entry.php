<?php

/**
 * Object representation of an entry (basic info, no values) - cf_form_entries
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry_Entry extends Caldera_Forms_Entry_Object {

	/** @var  string */
	protected $id;

	/** @var  string */
	protected $form_id;

	/** @var  string */
	protected $user_id;

	/** @var  string */
	protected $datestamp;

	/** @var  string */
	protected $status;

}
