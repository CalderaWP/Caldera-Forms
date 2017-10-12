<?php
/**
 * Caldera Forms - PHP Export 
 * Job Application 
 * @version    1.5.0-b-2
 * @license   GPL-2.0+
 * 
 */


 return array(
  '_last_updated' => 'Tue, 31 Jan 2017 13:03:37 +0000',
  'ID' => 'job-application',
  'cf_version' => '1.5.0-b-2',
  'name' => __( 'Job Application', 'caldera-forms' ),
  'scroll_top' => 1,
  'description' => '							',
  'success' => __( 'Form has been successfully submitted. Thank you.', 'caldera-forms' ),
  'db_support' => 1,
  'pinned' => 0,
  'hide_form' => 1,
  'check_honey' => 1,
  'avatar_field' => '',
  'form_ajax' => 1,
  'custom_callback' => '',
  'layout_grid' => 
  array(
    'fields' => 
    array(
      'fld_1529543' => '1:1',
      'fld_3688899' => '1:1',
      'fld_6829296' => '1:1',
      'fld_6830845' => '1:1',
      'fld_645468' => '2:1',
      'fld_193439' => '2:1',
      'fld_1720034' => '2:2',
      'fld_6566084' => '3:1',
      'fld_7007738' => '3:1',
      'fld_8684460' => '4:1',
      'fld_3540500' => '4:1',
      'fld_8862269' => '4:1',
      'fld_3202325' => '5:1',
      'fld_8728190' => '5:2',
    ),
    'structure' => '12|6:6|12#12|6:6',
  ),
  'fields' => 
  array(
    'fld_1529543' => 
    array(
      'ID' => 'fld_1529543',
      'type' => 'dropdown',
      'label' => __( 'Title', 'caldera-forms' ),
      'slug' => 'title',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'auto_type' => '',
        'taxonomy' => 'category',
        'post_type' => 'post',
        'value_field' => 'name',
        'orderby_tax' => 'name',
        'orderby_post' => 'name',
        'order' => 'ASC',
        'default' => 'opt1421110',
        'option' => 
        array(
          'opt1421110' => 
          array(
            'value' => 'Choose One',
            'label' => __( 'Choose One', 'caldera-forms' ),
          ),
          'opt1521658' => 
          array(
            'value' => 'Mr.',
            'label' => __( 'Mr.', 'caldera-forms' ),
          ),
          'opt1664029' => 
          array(
            'value' => 'Ms.',
            'label' => __( 'Ms.', 'caldera-forms' ),
          ),
          'opt1467463' => 
          array(
            'value' => 'Mrs.',
            'label' => __( 'Mrs.', 'caldera-forms' ),
          ),
          'opt1876827' => 
          array(
            'value' => 'Prof.',
            'label' => __( 'Prof.', 'caldera-forms' ),
          ),
          'opt1641302' => 
          array(
            'value' => 'Dr.',
            'label' => __( 'Dr.', 'caldera-forms' ),
          ),
        ),
      ),
    ),
    'fld_3688899' => 
    array(
      'ID' => 'fld_3688899',
      'type' => 'text',
      'label' => __( 'First Name', 'caldera-forms' ),
      'slug' => 'first_name',
      'conditions' => 
      array(
        'type' => '',
      ),
      'required' => 1,
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'default' => '',
        'type_override' => 'text',
        'mask' => '',
      ),
    ),
    'fld_6829296' => 
    array(
      'ID' => 'fld_6829296',
      'type' => 'text',
      'label' => __( 'Last Name', 'caldera-forms' ),
      'slug' => 'last_name',
      'conditions' => 
      array(
        'type' => '',
      ),
      'required' => 1,
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'default' => '',
        'type_override' => 'text',
        'mask' => '',
      ),
    ),
    'fld_6830845' => 
    array(
      'ID' => 'fld_6830845',
      'type' => 'email',
      'label' => __( 'Email Address', 'caldera-forms' ),
      'slug' => 'user_email',
      'conditions' => 
      array(
        'type' => '',
      ),
      'required' => 1,
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'default' => '',
      ),
    ),
    'fld_645468' => 
    array(
      'ID' => 'fld_645468',
      'type' => 'phone_better',
      'label' => __( 'Contact Number', 'caldera-forms' ),
      'slug' => 'home_number',
      'conditions' => 
      array(
        'type' => '',
      ),
      'required' => 1,
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'default' => '',
        'nationalMode' => 'on',
      ),
    ),
    'fld_193439' => 
    array(
      'ID' => 'fld_193439',
      'type' => 'phone_better',
      'label' => __( 'Alternate Contact Number', 'caldera-forms' ),
      'slug' => 'alternate_contact_number',
      'conditions' => 
      array(
        'type' => '',
      ),
      'required' => 1,
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'default' => '',
        'nationalMode' => 'on',
      ),
    ),
    'fld_1720034' => 
    array(
      'ID' => 'fld_1720034',
      'type' => 'advanced_file',
      'label' => __( 'Upload CV', 'caldera-forms' ),
      'slug' => 'cv',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => 'Please upload your CV in PDF format.',
      'config' => 
      array(
        'custom_class' => '',
        'attach' => 1,
        'multi_upload_text' => '',
        'allowed' => 'pdf',
      ),
    ),
    'fld_6566084' => 
    array(
      'ID' => 'fld_6566084',
      'type' => 'paragraph',
      'label' => __( 'Message / Comments', 'caldera-forms' ),
      'slug' => 'messagecomments',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'rows' => 4,
        'default' => '',
      ),
    ),
    'fld_7007738' => 
    array(
      'ID' => 'fld_7007738',
      'type' => 'button',
      'label' => __( 'Preview Information', 'caldera-forms' ),
      'slug' => 'preview_info',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'type' => 'next',
        'class' => 'btn btn-default',
        'target' => '',
      ),
    ),
    'fld_8684460' => 
    array(
      'ID' => 'fld_8684460',
      'type' => 'html',
      'label' => 'html_header_1',
      'slug' => 'html_header_1',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'default' => '<h2>Information Summary</h2>',
      ),
    ),
    'fld_3540500' => 
    array(
      'ID' => 'fld_3540500',
      'type' => 'live_gravatar',
      'label' => __( 'Profile', 'caldera-forms' ),
      'slug' => 'profile',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'email' => 'fld_6830845',
        'generator' => 'mystery',
        'size' => 100,
        'border_color' => '#efefef',
        'border_size' => 3,
        'border_radius' => 3,
      ),
    ),
    'fld_8862269' => 
    array(
      'ID' => 'fld_8862269',
      'type' => 'summary',
      'label' => __( 'Summary', 'caldera-forms' ),
      'hide_label' => 1,
      'slug' => 'summary',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
      ),
    ),
    'fld_3202325' => 
    array(
      'ID' => 'fld_3202325',
      'type' => 'button',
      'label' => __( 'Edit information', 'caldera-forms' ),
      'slug' => 'edit_information',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'type' => 'prev',
        'class' => 'btn btn-default',
        'target' => '',
      ),
    ),
    'fld_8728190' => 
    array(
      'ID' => 'fld_8728190',
      'type' => 'button',
      'label' => __( 'Submit', 'caldera-forms' ),
      'slug' => 'submit',
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
    1 => 'Page 2',
  ),
  'mailer' => 
  array(
    'on_insert' => 1,
    'sender_name' => 'Job Application Form',
    'sender_email' => 'myemail@email.com',
    'reply_to' => '',
    'email_type' => 'html',
    'recipients' => '',
    'bcc_to' => '',
    'email_subject' => 'Job Application',
    'email_message' => '{summary}',
  ),
  'conditional_groups' => 
  array(
  ),
  'settings' => 
  array(
    'responsive' => 
    array(
      'break_point' => 'sm',
    ),
  ),
  'version' => '1.5.0-b-2',
);
