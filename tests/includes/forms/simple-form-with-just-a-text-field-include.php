<?php
/**
 * Caldera Forms - PHP Export 
 * Simple Form With Just A Text Field 
 * @version    1.3.4-b1
 * @license   GPL-2.0+
 * 
 */


/**
 * Filter admin forms to include custom form in admin
 *
 * @since 1.3.4
 *
 * @param array all registered forms
 */
add_filter( "caldera_forms_get_forms", function( $forms ){
	$forms["simple-form-with-just-a-text-field"] = apply_filters( "caldera_forms_get_form-simple-form-with-just-a-text-field", array() );
	return $forms;
} );


add_filter( 'caldera_forms_get_form-simple-form-with-just-a-text-field', 'caldera_forms_tests_get_simple_form_with_just_a_text_field' );

/**
 * Filter form request to include form structure to be rendered
 *
 * @since 1.3.4
 *
 * @param array $form form structure
 *
 * @return array
 */
function caldera_forms_tests_get_simple_form_with_just_a_text_field( $form ) {

	return array(
		'_last_updated'      => 'Tue, 15 Mar 2016 22:06:15 +0000',
		'ID'                 => 'simple-form-with-just-a-text-field',
		'cf_version'         => '1.3.4-b1',
		'name'               => 'Simple Form With Just A Text Field',
		'description'        => '',
		'db_support'         => 1,
		'pinned'             => 0,
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
						'text_field' => '1:1',
					),
				'structure' => '12',
			),
		'fields'             =>
			array(
				'text_field' =>
					array(
						'ID'         => 'text_field',
						'type'       => 'text',
						'label'      => 'Text Field',
						'slug'       => 'text_field',
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
			),
		'page_names'         =>
			array(
				0 => 'Page 1',
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
				'sender_name'   => __( 'Caldera Forms Notification', 'caldera-forms' ),
				'sender_email'  => 'admin@local.dev',
				'reply_to'      => '',
				'email_type'    => 'html',
				'recipients'    => '',
				'bcc_to'        => '',
				'email_subject' => __( 'Simple Form With Just A Text Field', 'caldera-forms' ),
				'email_message' => '{summary}',
			),
	);
}
