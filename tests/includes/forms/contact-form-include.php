<?php
/**
 * Caldera Forms - PHP Export
 * Contact Form
 *
 * @version    1.3.4-b1
 * @license   GPL-2.0+
 *
 */


/**
 * Filter admin forms to include custom form in admin
 *
 * @since 1.3.4
 *
 * @param array $forms All registered forms
 */
add_filter( "caldera_forms_get_forms", function ( $forms ) {
	$forms[ "contact-form" ] = apply_filters( "caldera_forms_get_form-contact-form", array() );

	return $forms;
} );
add_filter( 'caldera_forms_get_form-contact-form', 'caldera_forms_tests_get_contact_form' );

/**
 * Filter form request to include form structure to be rendered
 *
 * @since 1.3.4
 *
 * @param $form array form structure
 *
 * @return array
 */
function caldera_forms_tests_get_contact_form( $form ) {
	return array(
		'name'         => 'Contact Form',
		'description'  => '',
		'db_support'   => 1,
		'hide_form'    => 1,
		'success'      => 'Form has been successfully submitted. Thank you.',
		'avatar_field' => 'email_address',
		'form_ajax'    => 1,
		'layout_grid'  =>
			array(
				'fields'    =>
					array(
						'header'             => '1:1',
						'first_name'         => '2:1',
						'last_name'          => '2:2',
						'email_address'      => '2:3',
						'message'            => '3:1',
						'comments_questions' => '4:1',
						'send_form'          => '5:1',
					),
				'structure' => '12|4:4:4|12|12|12',
			),
		'fields'       =>
			array(
				'header'             =>
					array(
						'ID'         => 'header',
						'type'       => 'html',
						'label'      => 'header',
						'slug'       => 'header',
						'caption'    => '',
						'config'     =>
							array(
								'custom_class' => '',
								'default'      => '<h2>Your Details</h2>
<p>Let us know how to get back to you.</p>
<hr>',
							),
						'conditions' =>
							array(
								'type' => '',
							),
					),
				'first_name'         =>
					array(
						'ID'         => 'first_name',
						'type'       => 'text',
						'label'      => 'First name',
						'slug'       => 'first_name',
						'required'   => '1',
						'caption'    => '',
						'config'     =>
							array(
								'custom_class' => '',
								'placeholder'  => '',
								'default'      => '',
								'mask'         => '',
							),
						'conditions' =>
							array(
								'type' => '',
							),
					),
				'last_name'          =>
					array(
						'ID'         => 'last_name',
						'type'       => 'text',
						'label'      => 'Last name',
						'slug'       => 'last_name',
						'required'   => '1',
						'caption'    => '',
						'config'     =>
							array(
								'custom_class' => '',
								'placeholder'  => '',
								'default'      => '',
								'mask'         => '',
							),
						'conditions' =>
							array(
								'type' => '',
							),
					),
				'email_address'      =>
					array(
						'ID'         => 'email_address',
						'type'       => 'email',
						'label'      => 'Email Address',
						'slug'       => 'email_address',
						'required'   => '1',
						'caption'    => '',
						'config'     =>
							array(
								'custom_class' => '',
								'placeholder'  => '',
								'default'      => '',
							),
						'conditions' =>
							array(
								'type' => '',
							),
					),
				'message'            =>
					array(
						'ID'         => 'message',
						'type'       => 'html',
						'label'      => 'message',
						'slug'       => 'message',
						'caption'    => '',
						'config'     =>
							array(
								'custom_class' => '',
								'default'      => '<h2>How can we help?</h2>
<p>Feel free to ask a question or simply leave a comment.</p>
<hr>',
							),
						'conditions' =>
							array(
								'type' => '',
							),
					),
				'comments_questions' =>
					array(
						'ID'         => 'comments_questions',
						'type'       => 'paragraph',
						'label'      => 'Comments / Questions',
						'slug'       => 'comments_questions',
						'required'   => '1',
						'caption'    => '',
						'config'     =>
							array(
								'custom_class' => '',
								'placeholder'  => '',
								'rows'         => '7',
								'default'      => '',
							),
						'conditions' =>
							array(
								'type' => '',
							),
					),
				'send_form'          =>
					array(
						'ID'         => 'send_form',
						'type'       => 'button',
						'label'      => 'Send Form',
						'slug'       => 'send_form',
						'caption'    => '',
						'config'     =>
							array(
								'custom_class' => '',
								'type'         => 'submit',
								'class'        => 'btn btn-default',
							),
						'conditions' =>
							array(
								'type' => '',
							),
					),
			),
		'page_names'   =>
			array(
				0 => 'Page 1',
			),
		'processors'   =>
			array(
				'fp_17689566' =>
					array(
						'ID'         => 'fp_17689566',
						'type'       => 'auto_responder',
						'config'     =>
							array(
								'sender_name'     => 'Site Admin',
								'sender_email'    => 'admin@localhost',
								'subject'         => 'Contact auto-response',
								'recipient_name'  => '%first_name% %last_name%',
								'recipient_email' => '%email_address%',
								'message'         => 'Hi %recipient_name%.
Thanks for your email.
We\'ll get get back to you as soon as possible!
Here\'s a summary of your message:
------------------------
{summary}',
							),
						'conditions' =>
							array(
								'type' => '',
							),
					),
			),
		'settings'     =>
			array(
				'responsive' =>
					array(
						'break_point' => 'sm',
					),
			),
		'mailer'       =>
			array(
				'on_insert' => 1,
			),
		'ID'           => 'contact-form',
		'check_honey'  => 1,
	);
}
