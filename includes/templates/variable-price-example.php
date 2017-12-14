<?php
/**
 * Caldera Forms - Variable pricing example
 *
 * @since 1.3.0
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <josh@calderawp.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock for CalderaWP LLC
 */

return array (
	'_last_updated' => 'Fri, 21 Aug 2015 00:33:50 +0000',
	'ID' => 'CF55d671ee14e40',
	'name' => __( 'Variable Price Example', 'caldera-forms' ),
	'description' => '',
	'db_support' => 1,
	'pinned' => 0,
	'hide_form' => 1,
	'check_honey' => 1,
	'success' => __( 'Form has been successfully submitted. Thank you.', 'caldera-forms' ),
	'avatar_field' => '',
	'form_ajax' => 1,
	'custom_callback' => '',
	'layout_grid' =>
		array (
			'fields' =>
				array (
					'fld_4338248' => '1:1',
					'fld_1316929' => '1:1',
					'fld_6796077' => '1:1',
					'fld_5987102' => '1:1',
					'fld_3993413' => '2:1',
					'fld_5161425' => '2:2',
					'fld_8997460' => '3:1',
					'fld_1338703' => '3:2',
				),
			'structure' => '12|6:6|6:6',
		),
	'fields' =>
		array (
			'fld_1316929' =>
				array (
					'ID' => 'fld_1316929',
					'type' => 'hidden',
					'label' => __( 'Value Big', 'caldera-forms' ),
					'slug' => 'value_big',
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'default' => 5,
						),
					'conditions' =>
						array (
							'type' => 'hide',
							'group' =>
								array (
									'rw19946608611' =>
										array (
											'cl3564295763' =>
												array (
													'field' => 'fld_5161425',
													'compare' => 'isnot',
													'value' => 'opt1533135',
												),
										),
								),
						),
				),
			'fld_4338248' =>
				array (
					'ID' => 'fld_4338248',
					'type' => 'hidden',
					'label' => __( 'One', 'caldera-forms' ),
					'slug' => 'one',
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'default' => 10,
						),
					'conditions' =>
						array (
							'type' => 'hide',
							'group' =>
								array (
									'rw47185924125' =>
										array (
											'cl5675243200' =>
												array (
													'field' => 'fld_3993413',
													'compare' => 'isnot',
													'value' => 'opt1697235',
												),
										),
								),
						),
				),
			'fld_5987102' =>
				array (
					'ID' => 'fld_5987102',
					'type' => 'hidden',
					'label' => __( 'Base', 'caldera-forms' ),
					'slug' => 'base',
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'default' => 25,
						),
					'conditions' =>
						array (
							'type' => '',
						),
				),
			'fld_3993413' =>
				array (
					'ID' => 'fld_3993413',
					'type' => 'checkbox',
					'label' => __( 'Want Option 1?', 'caldera-forms' ),
					'slug' => 'option_1',
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'inline' => 1,
							'auto_type' => '',
							'taxonomy' => 'category',
							'post_type' => 'post',
							'value_field' => 'name',
							'default' => '',
							'option' =>
								array (
									'opt1697235' =>
										array (
											'value' => '',
											'label' => __( 'Yes', 'caldera-forms' ),
										),
								),
						),
					'conditions' =>
						array (
							'type' => '',
						),
				),
			'fld_5161425' =>
				array (
					'ID' => 'fld_5161425',
					'type' => 'dropdown',
					'label' => __( 'Option 2 Type', 'caldera-forms' ),
					'slug' => 'option_2',
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'placeholder' => '',
							'auto_type' => '',
							'taxonomy' => 'category',
							'post_type' => 'post',
							'value_field' => 'name',
							'default' => '',
							'option' =>
								array (
									'opt1533135' =>
										array (
											'value' => '',
											'label' => __( 'Big', 'caldera-forms' ),
										),
									'opt1786217' =>
										array (
											'value' => '',
											'label' => __( 'Small', 'caldera-forms' ),
										),
								),
						),
					'conditions' =>
						array (
							'type' => '',
						),
				),
			'fld_8997460' =>
				array (
					'ID' => 'fld_8997460',
					'type' => 'calculation',
					'label' => __( 'Total', 'caldera-forms' ),
					'slug' => 'total',
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'element' => 'h3',
							'classes' => 'total-line',
							'before' => 'Total:',
							'after' => '',
							'fixed' => 1,
							'thousand_separator' => ',',
							'formular' => ' ( fld_5987102+fld_4338248+fld_6796077+fld_1316929 ) ',
							'config' =>
								array (
									'group' =>
										array (
											0 =>
												array (
													'lines' =>
														array (
															0 =>
																array (
																	'operator' => '+',
																	'field' => 'fld_5987102',
																),
															1 =>
																array (
																	'operator' => '+',
																	'field' => 'fld_4338248',
																),
															2 =>
																array (
																	'operator' => '+',
																	'field' => 'fld_6796077',
																),
															3 =>
																array (
																	'operator' => '+',
																	'field' => 'fld_1316929',
																),
														),
												),
										),
								),
							'manual_formula' => '',
						),
					'conditions' =>
						array (
							'type' => '',
						),
				),
			'fld_1338703' =>
				array (
					'ID' => 'fld_1338703',
					'type' => 'button',
					'label' => __( 'Pay', 'caldera-forms' ),
					'slug' => 'pay',
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
			'fld_6796077' =>
				array (
					'ID' => 'fld_6796077',
					'type' => 'hidden',
					'label' => __( 'Value Small', 'caldera-forms' ),
					'slug' => 'value_small',
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'default' => 1,
						),
					'conditions' =>
						array (
							'type' => 'hide',
							'group' =>
								array (
									'rw74415141932' =>
										array (
											'cl7168988428' =>
												array (
													'field' => 'fld_5161425',
													'compare' => 'isnot',
													'value' => 'opt1786217',
												),
										),
								),
						),
				),
		),
	'page_names' =>
		array (
			0 => 'Page 1',
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
			'enable_mailer' => 1,
			'sender_name' => __( 'Caldera Forms Notification', 'caldera-forms' ),
			'sender_email' => 'admin@local.dev',
			'reply_to' => '',
			'email_type' => 'html',
			'recipients' => '',
			'bcc_to' => '',
			'email_subject' => 'MultiPrice',
			'email_message' => '{summary}',
		),
);
