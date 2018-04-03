<?php

/**
 * Trait Caldera_Forms_Has_Mock_Form
 *
 * Add to tests that need a mock form.
 *
 * This trait MUST be kept free of WordPress/Caldera Forms dependencies.
 */
trait Caldera_Forms_Has_Mock_Form
{

	/**
	 * Mock form ID
	 *
	 * @since 1.6.1
	 * @var string
	 */
	protected $mock_form_id;

	/**
	 * A form that isn't saved or on filter to use as a mock
	 *
	 * @since 1.6.1
	 *
	 * @var array
	 */
	protected $mock_form;

	/**
	 * Set mock_form property
	 *
	 * @since 1.6.1
	 */
	private function set_mock_form(){
		$this->mock_form = array(
			'ID'                 => $this->mock_form_id,
			'name'               => 'Another form',
			'description'        => '',
			'db_support'         => 1,
			'pinned'             => 1,
			'hide_form'          => 1,
			'check_honey'        => 1,
			'success'            => __( 'Form has been successfully submitted. Thank you.', 'caldera-forms' ),
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
					'sender_email'  => 'roy@roysivan.taco',
					'reply_to'      => '',
					'email_type'    => 'html',
					'recipients'    => '',
					'bcc_to'        => '',
					'email_subject' => 'Hi Roy',
					'email_message' => 'Hi Mike',
				),
		);
	}


}