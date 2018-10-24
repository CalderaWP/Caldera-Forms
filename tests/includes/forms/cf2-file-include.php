<?php


/**
 * Hooks to load form.
 * Remove "caldera_forms_admin_forms" if you do not want this form to show in admin entry viewer
 */
add_filter( "caldera_forms_get_forms", "slug_register_caldera_forms_cf2file" );
add_filter( "caldera_forms_admin_forms", "slug_register_caldera_forms_cf2file" );
/**
 * Add form to front-end and admin
 *
 * @param array $forms All registered forms
 *
 * @return array
 */
function slug_register_caldera_forms_cf2file( $forms ) {
    $forms["cf2_file"] = apply_filters( "caldera_forms_get_form-cf2_file", array() );
    return $forms;
};

/**
 * Filter form request to include form structure to be rendered
 *
 * @since 1.3.1
 *
 * @param $form array form structure
 */
add_filter( 'caldera_forms_get_form-cf2_file', function( $form ){
    return array(
        '_last_updated' => 'Wed, 24 Oct 2018 19:34:12 +0000',
        'ID' => 'cf2_file',
        'cf_version' => '1.7.4',
        'name' => 'CF2 File',
        'scroll_top' => 0,
        'success' => 'Form has been successfully submitted. Thank you.			',
        'db_support' => 1,
        'pinned' => 0,
        'hide_form' => 1,
        'avatar_field' => NULL,
        'form_ajax' => 1,
        'custom_callback' => '',
        'layout_grid' =>
            array(
                'fields' =>
                    array(
                        'cf2_file_1' => '1:1',
                        'submit' => '1:1',
                    ),
                'structure' => '12',
            ),
        'fields' =>
            array(
                'cf2_file_1' =>
                    array(
                        'ID' => 'cf2_file_1',
                        'type' => 'cf2_file',
                        'label' => 'CF2_File_1',
                        'slug' => 'cf2_file_1',
                        'conditions' =>
                            array(
                                'type' => '',
                            ),
                        'caption' => '',
                        'config' =>
                            array(
                                'custom_class' => '',
                                'multi_upload_text' => '',
                                'allowed' => '',
                                'email_identifier' => 0,
                                'personally_identifying' => 0,
                            ),
                    ),
                'test_field_1' =>
                    array(
                        'ID' => 'test_field_1',
                        'type' => 'text',
                        'label' => 'test_field_1',
                        'slug' => 'test_field_1',
                        'conditions' =>
                            array(
                                'type' => '',
                            ),
                        'caption' => '',
                        'config' =>
                            array(

                            ),
                    ),
                'submit' =>
                    array(
                        'ID' => 'submit',
                        'type' => 'button',
                        'label' => 'Submit',
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
            ),
        'mailer' =>
            array(
                'on_insert' => 1,
                'sender_name' => 'Caldera Forms Notification',
                'sender_email' => 'test@test.com',
                'reply_to' => '',
                'email_type' => 'html',
                'recipients' => '',
                'bcc_to' => '',
                'email_subject' => 'CF2 File',
                'email_message' => '{summary}',
            ),
        'check_honey' => 1,
        'antispam' =>
            array(
                'sender_name' => '',
                'sender_email' => '',
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
        'privacy_exporter_enabled' => false,
        'version' => '1.7.4',
        'db_id' => '33',
        'type' => 'primary',
        '_external_form' => 1,
    );
} );
