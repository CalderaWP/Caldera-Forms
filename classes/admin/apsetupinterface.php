<?php

/**
 * Interface for adding auto-populate options to select fields
 *
 * Both methods are hook callbacks
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
interface Caldera_Forms_Admin_APSetupInterface {

	/**
	 * Add the type option element
	 *
	 * @uses "caldera_forms_autopopulate_types" action
	 *
	 * @since 1.4.3
	 */
	public function add_type();

	/**
	 * Add the options for what to aut-populate by
	 *
	 * @uses "caldera_forms_autopopulate_type_config" action
	 *
	 * @since 1.4.3
	 */
	public function add_options();

}