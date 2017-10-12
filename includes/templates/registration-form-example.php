<?php
/**
 * Caldera Forms - Registration form example.
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
	'_last_updated' => 'Fri, 21 Aug 2015 20:42:39 +0000',
	'ID' => 'CF55d78d3e3624f',
	'name' => __( 'Registration Form', 'caldera-forms' ),
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
					'fld_1327818' => '1:1',
					'fld_1812913' => '2:1',
					'fld_7290902' => '2:2',
					'fld_6900741' => '3:1',
					'fld_1561974' => '3:2',
					'fld_6241168' => '4:1',
					'fld_2458053' => '4:2',
					'fld_2668148' => '5:1',
				),
			'structure' => '12|6:6|6:6|6:6|6:6',
		),
	'fields' =>
		array (
			'fld_1327818' =>
				array (
					'ID' => 'fld_1327818',
					'type' => 'toggle_switch',
					'label' => __( 'Number of People', 'caldera-forms' ),
					'slug' => 'number_of_people',
					'conditions' =>
						array (
							'type' => '',
						),
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'orientation' => 'horizontal',
							'selected_class' => 'btn-success',
							'default_class' => 'btn-default',
							'auto_type' => '',
							'taxonomy' => 'category',
							'post_type' => 'post',
							'value_field' => 'name',
							'default' => 'opt1186018',
							'option' =>
								array (
									'opt1186018' =>
										array (
											'value' => '',
											'label' => __( 'One', 'caldera-forms' ),
										),
									'opt1400463' =>
										array (
											'value' => '',
											'label' => __( 'Two', 'caldera-forms' ),
										),
									'opt1453751' =>
										array (
											'value' => '',
											'label' => __( 'Three', 'caldera-forms' ),
										),
								),
						),
				),
			'fld_1812913' =>
				array (
					'ID' => 'fld_1812913',
					'type' => 'text',
					'label' => __( 'Person One Name', 'caldera-forms' ),
					'slug' => 'person_one_name',
					'conditions' =>
						array (
							'type' => '',
						),
					'required' => 1,
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'placeholder' => '',
							'default' => '',
							'mask' => '',
						),
				),
			'fld_7290902' =>
				array (
					'ID' => 'fld_7290902',
					'type' => 'email',
					'label' => __( 'Person One Email', 'caldera-forms' ),
					'slug' => 'person_one_email',
					'conditions' =>
						array (
							'type' => '',
						),
					'required' => 1,
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'placeholder' => '',
							'default' => '',
						),
				),
			'fld_6900741' =>
				array (
					'ID' => 'fld_6900741',
					'type' => 'text',
					'label' => __( 'Person Two Name', 'caldera-forms' ),
					'slug' => 'person_two_name',
					'conditions' =>
						array (
							'type' => 'con_9457156563497745',
						),
					'required' => 1,
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'placeholder' => '',
							'default' => '',
							'mask' => '',
						),
				),
			'fld_1561974' =>
				array (
					'ID' => 'fld_1561974',
					'type' => 'email',
					'label' => __( 'Person Two Email', 'caldera-forms' ),
					'slug' => 'person_two_email',
					'conditions' =>
						array (
							'type' => 'con_9457156563497745',
						),
					'required' => 1,
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'placeholder' => '',
							'default' => '',
						),
				),
			'fld_2458053' =>
				array (
					'ID' => 'fld_2458053',
					'type' => 'email',
					'label' => __( 'Person Three Email', 'caldera-forms' ),
					'slug' => 'person_three_email',
					'conditions' =>
						array (
							'type' => 'con_4812944559945067',
						),
					'required' => 1,
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'placeholder' => '',
							'default' => '',
						),
				),
			'fld_6241168' =>
				array (
					'ID' => 'fld_6241168',
					'type' => 'text',
					'label' => __( 'Person Three Name', 'caldera-forms' ),
					'slug' => 'person_three_name',
					'conditions' =>
						array (
							'type' => 'con_4812944559945067',
						),
					'required' => 1,
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'placeholder' => '',
							'default' => '',
							'mask' => '',
						),
				),
			'fld_2668148' =>
				array (
					'ID' => 'fld_2668148',
					'type' => 'button',
					'label' => __( 'Submit', 'caldera-forms' ),
					'slug' => 'submit',
					'conditions' =>
						array (
							'type' => '',
						),
					'caption' => '',
					'config' =>
						array (
							'custom_class' => '',
							'type' => 'submit',
							'class' => 'btn btn-default',
						),
				),
		),
	'page_names' =>
		array (
			0 => 'Page 1',
		),
	'conditional_groups' =>
		array (
			'_open_condition' => 'con_4812944559945067',
			'conditions' =>
				array (
					'con_9457156563497745' =>
						array (
							'id' => 'con_9457156563497745',
							'name' => __( 'Two People', 'caldera-forms' ),
							'type' => 'show',
							'fields' =>
								array (
									'cl8962595065547459' => 'fld_1327818',
									'cl4510708085080191' => 'fld_1327818',
								),
							'group' =>
								array (
									'rw872285474363952' =>
										array (
											'cl8962595065547459' =>
												array (
													'parent' => 'rw872285474363952',
													'field' => 'fld_1327818',
													'compare' => 'is',
													'value' => 'opt1400463',
												),
										),
									'rw6265972925354504' =>
										array (
											'cl4510708085080191' =>
												array (
													'parent' => 'rw6265972925354504',
													'field' => 'fld_1327818',
													'compare' => 'is',
													'value' => 'opt1453751',
												),
										),
								),
						),
					'con_4812944559945067' =>
						array (
							'id' => 'con_4812944559945067',
							'name' => __( 'Three People', 'caldera-forms' ),
							'type' => 'show',
							'group' =>
								array (
									'rw1885912614213066' =>
										array (
											'cl5077669325217868' =>
												array (
													'parent' => 'rw1885912614213066',
													'field' => 'fld_1327818',
													'compare' => 'is',
													'value' => 'opt1453751',
												),
										),
								),
							'fields' =>
								array (
									'cl5077669325217868' => 'fld_1327818',
								),
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
			'enable_mailer' => 1,
			'sender_name' => __( 'Caldera Forms Notification', 'caldera-forms' ),
			'sender_email' => 'admin@local.dev',
			'reply_to' => '',
			'email_type' => 'html',
			'recipients' => '',
			'bcc_to' => '',
			'email_subject' => 'Registration',
			'email_message' => '{summary}',
		),
);
