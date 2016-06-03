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

	protected $id;
	protected $form_id;
	protected $user_id;
	protected $datestamp;
	protected $status;

}
