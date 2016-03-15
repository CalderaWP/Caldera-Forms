<?php
/**
 * Base test case class
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
abstract class Caldera_Forms_Test_Case extends WP_UnitTestCase {

	/**
	 * Forms setup using filter
	 *
	 * @since 1.3.4
	 *
	 * @var array
	 */
	protected $forms_on_filters = array(
		'simple-form-with-just-a-text-field',
		'contact-form'
	);


	/**
	 * A form that isn't saved or on filter to use as a mock
	 *
	 * @since 1.3.4
	 *
	 * @var array
	 */
	protected $mock_form = array(
		'_last_updated'      => 'Tue, 15 Mar 2016 22:16:51 +0000',
		'ID'                 => 'another-form',
		'cf_version'         => '1.3.4-b1',
		'name'               => 'Another form',
		'description'        => '',
		'db_support'         => 1,
		'pinned'             => 0,
		'hide_form'          => 1,
		'check_honey'        => 1,
		'success'            => 'Form has been successfully submitted. Thank you.',
		'avatar_field'       => null,
		'form_ajax'          => 1,
		'custom_callback'    => '',
		'layout_grid'        =>
			array(
				'fields'    =>
					array(
						'fld_1724450' => '1:1',
						'fld_6125005' => '1:2',
						'fld_7269029' => '2:1',
						'fld_7896909' => '3:1',
					),
				'structure' => '6:6|12#12',
			),
		'fields'             =>
			array(
				'fld_1724450' =>
					array(
						'ID'         => 'fld_1724450',
						'type'       => 'text',
						'label'      => 'Text',
						'slug'       => 'text',
						'conditions' =>
							array(
								'type' => '',
							),
						'caption'    => '',
						'config'     =>
							array(
								'custom_class'  => '',
								'placeholder'   => '',
								'default'       => '',
								'mask'          => '',
								'type_override' => 'text',
							),
					),
				'fld_6125005' =>
					array(
						'ID'         => 'fld_6125005',
						'type'       => 'email',
						'label'      => 'Email',
						'slug'       => 'email',
						'conditions' =>
							array(
								'type' => '',
							),
						'required'   => 1,
						'caption'    => 'Make emails',
						'entry_list' => 1,
						'config'     =>
							array(
								'custom_class' => '',
								'placeholder'  => '',
								'default'      => '',
							),
					),
				'fld_7269029' =>
					array(
						'ID'         => 'fld_7269029',
						'type'       => 'button',
						'label'      => 'Next Page',
						'slug'       => 'next_page',
						'conditions' =>
							array(
								'type' => '',
							),
						'caption'    => '',
						'config'     =>
							array(
								'custom_class' => '',
								'type'         => 'next',
								'class'        => 'btn btn-default',
								'target'       => '',
							),
					),
				'fld_7896909' =>
					array(
						'ID'         => 'fld_7896909',
						'type'       => 'button',
						'label'      => 'Submit',
						'slug'       => 'submit',
						'conditions' =>
							array(
								'type' => '',
							),
						'caption'    => '',
						'config'     =>
							array(
								'custom_class' => '',
								'type'         => 'submit',
								'class'        => 'btn btn-default',
								'target'       => '',
							),
					),
			),
		'page_names'         =>
			array(
				0 => 'Page 1',
				1 => 'Page 2',
			),
		'conditional_groups' =>
			array(
				'fields' =>
					array(),
			),
		'settings'           =>
			array(
				'responsive' =>
					array(
						'break_point' => 'sm',
					),
			),
		'mailer'             =>
			array(
				'on_insert'     => 1,
				'sender_name'   => 'Caldera Forms Notification',
				'sender_email'  => 'admin@local.dev',
				'reply_to'      => '',
				'email_type'    => 'html',
				'recipients'    => '',
				'bcc_to'        => '',
				'email_subject' => 'Another form',
				'email_message' => '{summary}',
			),
	);
}
