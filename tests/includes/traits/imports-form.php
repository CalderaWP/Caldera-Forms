<?php


trait Caldera_Forms_Imports_Form
{

    /**
     * Import contact form without auto-responder
     *
     * @since 1.5.9
     *
     * @param bool $main_mailer Optional. If true, the default, contact form for main mailer is imported. If false, contact form for auti-responder is imported.
     * @return string
     */
    protected function import_contact_form($main_mailer = true ){
        if ($main_mailer) {
            $file = $this->get_path_for_main_mailer_form_import();
        } else {
            $file = $this->get_path_for_auto_responder_contact_form_import();

        }

        return $this->import_form($file);
    }

    /**
     * Import form by file path
     *
     * @since 1.5.9
     *
     * @param string $file Path to form config
     * @return string
     */
    protected function import_form($file) {
        $json = file_get_contents($file);
        $config = $this->recursive_cast_array(json_decode($json));
        $form_id = Caldera_Forms_Forms::import_form($config);
        return $form_id;
    }

    /**
     * Get file path for JSON export we import for contact form main mailer tests
     *
     * @since 1.6.0
     *
     * @return string
     */
    protected function get_path_for_main_mailer_form_import(){
        return $file = dirname(__FILE__, 2) . '/forms/contact-forms-no-auto-responder.json';
    }

    /**
     * Get file path for JSON export we import for contact form auto-responder tests
     *
     * @since 1.6.0
     *
     * @return string
     */
    protected function get_path_for_auto_responder_contact_form_import(){
        return dirname(__FILE__,2) . '/forms/contact-form-autoresponder.json';
    }

    /**
     * Cast array or object, like a form import, to array
     *
     * @since 1.6.0
     *
     * @param $array_or_object
     * @return array
     */
    protected function recursive_cast_array( $array_or_object ){
        $array_or_object = (array) $array_or_object;
        foreach ( $array_or_object as $key => $value ){
            if( is_array( $value ) || is_object( $value ) ){
                $array_or_object[ $key ] = $this->recursive_cast_array( $value );
            }

        }
        return $array_or_object;
    }

}