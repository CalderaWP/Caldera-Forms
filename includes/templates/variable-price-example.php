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
	'_last_updated' => 'Wed, 27 Feb 2019 11:25:09 +0000',
	'ID' => 'variable_price_example',
	'cf_version' => '1.8.0',
	'name' => __( 'Variable Price Example', 'caldera-forms' ),
	'scroll_top' => 0,
	'success' => __( 'Form has been successfully submitted. Thank you.', 'caldera-forms' ),
	'db_support' => 1,
	'pinned' => 0,
	'hide_form' => 1,
	'avatar_field' => '',
	'form_ajax' => 1,
	'custom_callback' => '',
	'layout_grid' =>
		array(
			'fields' =>
				array(
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
		array(
			'fld_4338248' =>
				array(
					'ID' => 'fld_4338248',
					'type' => 'hidden',
					'label' => __( 'One', 'caldera-forms' ),
					'slug' => 'one',
					'conditions' =>
						array(
							'type' => 'con_6963126440453143',
						),
					'caption' => '',
					'config' =>
						array(
							'custom_class' => '',
							'default' => 10,
							'email_identifier' => 0,
							'personally_identifying' => 0,
						),
				),
			'fld_1316929' =>
				array(
					'ID' => 'fld_1316929',
					'type' => 'hidden',
					'label' => __( 'Value Big', 'caldera-forms' ),
					'slug' => 'value_big',
					'conditions' =>
						array(
							'type' => 'con_62305476271984',
						),
					'caption' => '',
					'config' =>
						array(
							'custom_class' => '',
							'default' => 5,
							'email_identifier' => 0,
							'personally_identifying' => 0,
						),
				),
			'fld_6796077' =>
				array(
					'ID' => 'fld_6796077',
					'type' => 'hidden',
					'label' => __( 'Value Small', 'caldera-forms' ),
					'slug' => 'value_small',
					'conditions' =>
						array(
							'type' => 'con_3741474168794045',
						),
					'caption' => '',
					'config' =>
						array(
							'custom_class' => '',
							'default' => 1,
							'email_identifier' => 0,
							'personally_identifying' => 0,
						),
				),
			'fld_5987102' =>
				array(
					'ID' => 'fld_5987102',
					'type' => 'hidden',
					'label' => __( 'Base', 'caldera-forms' ),
					'slug' => 'base',
					'conditions' =>
						array(
							'type' => '',
						),
					'caption' => '',
					'config' =>
						array(
							'custom_class' => '',
							'default' => 25,
							'email_identifier' => 0,
							'personally_identifying' => 0,
						),
				),
			'fld_3993413' =>
				array(
					'ID' => 'fld_3993413',
					'type' => 'checkbox',
					'label' => __( 'Want Option 1?', 'caldera-forms' ),
					'slug' => 'option_1',
					'conditions' =>
						array(
							'type' => '',
						),
					'caption' => '',
					'config' =>
						array(
							'custom_class' => '',
							'inline' => 1,
							'default_option' => '',
							'auto_type' => '',
							'taxonomy' => 'category',
							'post_type' => 'post',
							'value_field' => 'name',
							'orderby_tax' => 'name',
							'orderby_post' => 'name',
							'order' => 'ASC',
							'default' => '',
							'option' =>
								array(
									'opt1697235' =>
										array(
											'calc_value' => 'Yes',
											'value' => 'Yes',
											'label' => __( 'Yes', 'caldera-forms' ),
										),
								),
							'email_identifier' => 0,
							'personally_identifying' => 0,
						),
				),
			'fld_5161425' =>
				array(
					'ID' => 'fld_5161425',
					'type' => 'dropdown',
					'label' => __('Option 2 Type', 'caldera-forms' ),
					'slug' => 'option_2',
					'conditions' =>
						array(
							'type' => '',
						),
					'caption' => '',
					'config' =>
						array(
							'custom_class' => '',
							'placeholder' => '',
							'default_option' => '',
							'auto_type' => '',
							'taxonomy' => 'category',
							'post_type' => 'post',
							'value_field' => 'name',
							'orderby_tax' => 'name',
							'orderby_post' => 'name',
							'order' => 'ASC',
							'default' => '',
							'option' =>
								array(
									'opt1533135' =>
										array(
											'calc_value' => 'Big',
											'value' => 'Big',
											'label' => __( 'Big', 'caldera-forms' ),
										),
									'opt1786217' =>
										array(
											'calc_value' => 'Small',
											'value' => 'Small',
											'label' => __( 'Small', 'caldera-forms' ),
										),
								),
							'email_identifier' => 0,
							'personally_identifying' => 0,
						),
				),
			'fld_8997460' =>
				array(
					'ID' => 'fld_8997460',
					'type' => 'calculation',
					'label' => __( 'Total', 'caldera-forms' ),
					'slug' => 'total',
					'conditions' =>
						array(
							'type' => '',
						),
					'caption' => '',
					'config' =>
						array(
							'custom_class' => '',
							'element' => 'h3',
							'classes' => 'total-line',
							'before' => 'Total:',
							'after' => '',
							'fixed' => 1,
							'thousand_separator' => ',',
							'decimal_separator' => '.',
							'formular' => ' ( fld_5987102+fld_4338248+fld_6796077+fld_1316929 ) ',
							'config' =>
								array(
									'group' =>
										array(
											0 =>
												array(
													'lines' =>
														array(
															0 =>
																array(
																	'operator' => '+',
																	'field' => 'fld_5987102',
																),
															1 =>
																array(
																	'operator' => '+',
																	'field' => 'fld_4338248',
																),
															2 =>
																array(
																	'operator' => '+',
																	'field' => 'fld_6796077',
																),
															3 =>
																array(
																	'operator' => '+',
																	'field' => 'fld_1316929',
																),
														),
												),
										),
								),
							'manual_formula' => '',
							'email_identifier' => 0,
							'personally_identifying' => 0,
						),
				),
			'fld_1338703' =>
				array(
					'ID' => 'fld_1338703',
					'type' => 'button',
					'label' => __( 'Pay', 'caldera-forms' ),
					'slug' => 'pay',
					'conditions' =>
						array(
							'type' => '',
						),
					'caption' => '',
					'config' =>
						array(
							'custom_class' => '',
							'type' => 'submit',
							'class' => 'btn btn-default',
							'target' => '',
						),
				),
		),
	'page_names' =>
		array(
			0 => 'Page 1',
		),
	'mailer' =>
		array(
			'on_insert' => 1,
			'sender_name' => __( 'Caldera Forms Notification', 'caldera-forms' ),
			'sender_email' => '',
			'reply_to' => '',
			'email_type' => 'html',
			'recipients' => '',
			'bcc_to' => '',
			'email_subject' => 'Web Form',
			'email_message' => '{summary}
<div style="display: none;"></div>',
		),
	'check_honey' => 1,
	'antispam' =>
		array(
			'sender_name' => '',
			'sender_email' => '',
		),
	'conditional_groups' =>
		array(
			'conditions' =>
				array(
					'con_6963126440453143' =>
						array(
							'id' => 'con_6963126440453143',
							'name' => 'Option one',
							'type' => 'show',
							'fields' =>
								array(
									'cl9601054985524611' => 'fld_3993413',
								),
							'group' =>
								array(
									'rw9778182194626583' =>
										array(
											'cl9601054985524611' =>
												array(
													'parent' => 'rw9778182194626583',
													'field' => 'fld_3993413',
													'compare' => 'is',
													'value' => 'opt1697235',
												),
										),
								),
						),
					'con_62305476271984' =>
						array(
							'id' => 'con_62305476271984',
							'name' => 'Option Big',
							'type' => 'show',
							'fields' =>
								array(
									'cl229834406512118' => 'fld_5161425',
								),
							'group' =>
								array(
									'rw3650331132355743' =>
										array(
											'cl229834406512118' =>
												array(
													'parent' => 'rw3650331132355743',
													'field' => 'fld_5161425',
													'compare' => 'is',
													'value' => 'opt1533135',
												),
										),
								),
						),
					'con_3741474168794045' =>
						array(
							'id' => 'con_3741474168794045',
							'name' => 'Option small',
							'type' => 'show',
							'group' =>
								array(
									'rw262828443167786' =>
										array(
											'cl7323939766916735' =>
												array(
													'parent' => 'rw262828443167786',
													'field' => 'fld_5161425',
													'compare' => 'is',
													'value' => 'opt1786217',
												),
										),
								),
							'fields' =>
								array(
									'cl7323939766916735' => 'fld_5161425',
								),
						),
				),
		),
	'settings' =>
		array(
			'responsive' =>
				array(
					'break_point' => 'sm',
				),
		),
	'privacy_exporter_enabled' => false,
	'version' => '1.8.0',
	'db_id' => '216',
	'type' => 'primary',
	'_external_form' => 1,
);
