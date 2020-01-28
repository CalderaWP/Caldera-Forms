<?php

return array(
    '_last_updated' => 'Tue, 28 Jan 2020 16:39:08 +0000',
    'ID' => 'saved-entry-test',
    'cf_version' => '1.8.9',
    'name' => 'Saved Entry Test',
    'scroll_top' => 0,
    'success' => 'Form has been successfully submitted. Thank you.			',
    'db_support' => 1,
    'pinned' => 0,
    'hide_form' => 1,
    'check_honey' => 1,
    'avatar_field' => NULL,
    'form_ajax' => 1,
    'custom_callback' => '',
    'layout_grid' =>
        array(
            'fields' =>
                array(
                    'name' => '1:1',
                    'show_name' => '1:2',
                    'submit_form' => '2:1',
                ),
            'structure' => '6:6|12',
        ),
    'fields' =>
        array(
            'name' =>
                array(
                    'ID' => 'name',
                    'type' => 'text',
                    'label' => 'Name',
                    'slug' => 'name',
                    'conditions' =>
                        array(
                            'type' => 'con_2433725567374679',
                        ),
                    'caption' => '',
                    'config' =>
                        array(
                            'custom_class' => '',
                            'placeholder' => '',
                            'default' => '',
                            'type_override' => 'text',
                            'mask' => '',
                            'email_identifier' => 0,
                            'personally_identifying' => 0,
                        ),
                ),
            'show_name' =>
                array(
                    'ID' => 'show_name',
                    'type' => 'dropdown',
                    'label' => 'Show Name',
                    'slug' => 'show_name',
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
                            'orderby_tax' => 'count',
                            'orderby_post' => 'ID',
                            'order' => 'ASC',
                            'show_values' => 1,
                            'default' => 'opt180303',
                            'option' =>
                                array(
                                    'opt180303' =>
                                        array(
                                            'calc_value' => 1,
                                            'value' => 'yes',
                                            'label' => 'Yes',
                                        ),
                                    'opt1080256' =>
                                        array(
                                            'calc_value' => 0,
                                            'value' => 'no',
                                            'label' => 'No',
                                        ),
                                ),
                            'email_identifier' => 0,
                            'personally_identifying' => 0,
                        ),
                ),
            'submit_form' =>
                array(
                    'ID' => 'submit_form',
                    'type' => 'button',
                    'label' => 'Submit Form',
                    'slug' => 'submit_form',
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
            'email_subject' => 'Saved Entry Test',
            'email_message' => '{summary}',
        ),
    'conditional_groups' =>
        array(
            'conditions' =>
                array(
                    'con_2433725567374679' =>
                        array(
                            'id' => 'con_2433725567374679',
                            'name' => 'Show Name',
                            'type' => 'show',
                            'group' =>
                                array(
                                    'rw98475241696795' =>
                                        array(
                                            'cl449356914074560' =>
                                                array(
                                                    'parent' => 'rw98475241696795',
                                                    'field' => 'show_name',
                                                    'compare' => 'is',
                                                    'value' => 'opt180303',
                                                ),
                                        ),
                                ),
                            'fields' =>
                                array(
                                    'cl449356914074560' => 'show_name',
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
    'version' => '1.8.9',
    'db_id' => '10',
    'type' => 'primary',
    '_external_form' => 1,
);

