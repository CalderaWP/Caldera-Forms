<?php
/**
 * Caldera Forms - starter template
 *
 * @package   Caldera_Forms
 * @author    David <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 David Cramer
 */

return array (
	'name' => 'Contact Form',
	'description' => 'Basic starter contact form with auto-responder processor.',
	'db_support' => '1',
	'hide_form' => '1',
	'success' => 'Thank you, your message has been sent.',
	'avatar_field' => 'fld_6009157',
	'form_ajax' => '1',
	'layout_grid' => 
	array (
		'fields' => 
		array (
			'fld_29462' => '1:1',
			'fld_8768091' => '2:1',
			'fld_9970286' => '2:2',
			'fld_6009157' => '2:3',
			'fld_2758980' => '3:1',
			'fld_7683514' => '4:1',
			'fld_7908577' => '5:1',
		),
		'structure' => '12|4:4:4|12|12|12',
	),
	'fields' => 
	array (
		'fld_29462' => 
		array (
			'ID' => 'fld_29462',
			'type' => 'html',
			'label' => 'header',
			'slug' => 'header',
			'caption' => '',
			'config' => 
			array (
				'custom_class' => '',
				'default' => '<h2>Your Details</h2>
<p>Let us know how to get back to you.</p>
<hr>',
			),
			'conditions' => 
			array (
				'type' => '',
			),
		),
		'fld_8768091' => 
		array (
			'ID' => 'fld_8768091',
			'type' => 'text',
			'label' => 'First name',
			'slug' => 'first_name',
			'required' => '1',
			'caption' => '',
			'config' => 
			array (
				'custom_class' => '',
				'placeholder' => '',
				'default' => '',
				'mask' => '',
			),
			'conditions' => 
			array (
				'type' => '',
			),
		),
		'fld_9970286' => 
		array (
			'ID' => 'fld_9970286',
			'type' => 'text',
			'label' => 'Last name',
			'slug' => 'last_name',
			'required' => '1',
			'caption' => '',
			'config' => 
			array (
				'custom_class' => '',
				'placeholder' => '',
				'default' => '',
				'mask' => '',
			),
			'conditions' => 
			array (
				'type' => '',
			),
		),
		'fld_6009157' => 
		array (
			'ID' => 'fld_6009157',
			'type' => 'email',
			'label' => 'Email Address',
			'slug' => 'email_address',
			'required' => '1',
			'caption' => '',
			'config' => 
			array (
				'custom_class' => '',
				'placeholder' => '',
				'default' => '',
			),
			'conditions' => 
			array (
				'type' => '',
			),
		),
		'fld_2758980' => 
		array (
			'ID' => 'fld_2758980',
			'type' => 'html',
			'label' => 'message',
			'slug' => 'message',
			'caption' => '',
			'config' => 
			array (
				'custom_class' => '',
				'default' => '<h2>How can we help?</h2>
<p>Feel free to ask a question or simply leave a comment.</p>
<hr>',
			),
			'conditions' => 
			array (
				'type' => '',
			),
		),
		'fld_7683514' => 
		array (
			'ID' => 'fld_7683514',
			'type' => 'paragraph',
			'label' => 'Comments / Questions',
			'slug' => 'comments_questions',
			'required' => '1',
			'caption' => '',
			'config' => 
			array (
				'custom_class' => '',
				'placeholder' => '',
				'rows' => '7',
				'default' => '',
			),
			'conditions' => 
			array (
				'type' => '',
			),
		),
		'fld_7908577' => 
		array (
			'ID' => 'fld_7908577',
			'type' => 'button',
			'label' => 'Send Message',
			'slug' => 'submit',
			'caption' => '',
			'config' => 
			array (
				'custom_class' => '',
				'type' => 'submit',
				'class' => 'btn btn-default',
			),
			'conditions' => 
			array (
				'type' => '',
			),
		),
	),
	'page_names' => 
	array (
		0 => 'Page 1',
	),
	'processors' => 
	array (
		'fp_17689566' => 
		array (
			'ID' => 'fp_17689566',
			'type' => 'auto_responder',
			'config' => 
			array (
				'sender_name' => get_option( 'blogname' ),
				'sender_email' => get_option( 'admin_email' ),
				'subject' => 'Thanks For Getting In Touch',
				'recipient_name' => '%first_name% %last_name%',
				'recipient_email' => '%email_address%',
				'message' => 'Hi %recipient_name%.
Thanks for your email.
We\'ll get get back to you as soon as possible!
Here\'s a summary of your message:
------------------------
{summary}',
			),
			'conditions' => 
			array (
				'type' => '',
			),
		),
	),
	'settings' => 
	array (
		'responsive' => 
		array (
			'break_point' => 'sm',
		),
	),
	'mailer' => 
	array (
		'enable_mailer' => '1',
		'sender_name' => 'Contact Form Submission',
		'sender_email' => '%email_address',
		'email_type' => 'html',
		'recipients' => get_option( 'admin_email' ),
		'email_subject' => 'Contact Form',
		'email_message' => '{summary}',
	),
);
