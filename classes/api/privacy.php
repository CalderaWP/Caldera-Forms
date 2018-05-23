<?php

/**
 * Class Caldera_Forms_API_Privacy
 *
 * Form config abstraction for working with privacy settings of form
 */
class Caldera_Forms_API_Privacy extends Caldera_Forms_API_Form
{
    /**
     * Report if this form has privacy/GDPR exporter enabled
     *
     * @since 1.7.0
     *
     * @return true
     */
    public function is_privacy_exporter_enabled()
    {
        return Caldera_Forms_Forms::is_privacy_export_enabled( $this->form );
    }

    /**
     * Enable privacy export for this form
     *
     * @since 1.7.0
     *
     * @return Caldera_Forms_API_Privacy
     */
    public function enable_privacy_exporter()
    {
        $this->form = Caldera_Forms_Forms::update_privacy_export_enabled($this->form, true );
        return $this->save_form();
    }

    /**
     * Disable privacy export for this form.
     *
     * @return Caldera_Forms_API_Privacy
     */
    public function disable_privacy_exporter()
    {
        $this->form = Caldera_Forms_Forms::update_privacy_export_enabled($this->form, false );
        return $this->save_form();
    }

    /**
     * Get IDs of the fields that can contain personally identifying fields
     *
     * @since 1.7.0
     *
     * @return array
     */
    public function get_pii_fields()
    {
        return Caldera_Forms_Forms::personally_identifying_fields($this->form, true );
    }

    /**
     * Get IDs of the fields that can contain email we can use to identify whose personally identifying info a form contains.
     *
     * @since 1.7.0
     *
     * @return array
     */
    public function get_email_identifying_fields()
    {
        return Caldera_Forms_Forms::email_identifying_fields($this->form, true );
    }

    /**
     * (re)set PII fields of form
     *
     * @since 1.7.0
     *
     * @param array $pii_fields New value
     * @return $this
     */
    public function set_pii_fields($pii_fields ){
        foreach( $this->get_fields() as $field ){
            $this->form[ 'fields' ][ $field[ 'ID' ] ][ 'config' ][ Caldera_Forms_Field_Util::CONFIG_PERSONAL] = (int) in_array( $field[ 'ID' ], $pii_fields );
        }
        $this->set_fields();
        return $this;
    }

    /**
     * (re)set email identifying field(s) of form
     *
     * @since 1.7.0
     *
     * @param array $email_fields New value
     * @return $this
     */
    public function set_email_identifying_fields( $email_fields )
    {
        foreach( $this->get_fields() as $field ){
            $this->form[ 'fields' ][ $field[ 'ID' ] ][ 'config' ][ Caldera_Forms_Field_Util::CONFIG_EMAIL_IDENTIFIER ] = (int) in_array( $field[ 'ID' ], $email_fields );
        }
        $this->set_fields();
        return $this;
    }

    /** @inheritdoc */
    public function toArray()
    {
        return array(
            'ID' => caldera_forms_very_safe_string($this->form[ 'ID' ]),
            'name' => isset( $this->form[ 'name' ] ) ? caldera_forms_very_safe_string( $this->form[ 'name' ] ) : '',
            'fields' => $this->get_fields(),
            'emailIdentifyingFields' => $this->get_email_identifying_fields(),
            'piiFields' => $this->get_pii_fields(),
            'privacyExporterEnabled' => $this->is_privacy_exporter_enabled()
        );
    }

    /** @inheritdoc */
    public function set_fields()
    {
        foreach (Caldera_Forms_Forms::get_fields($this->form, true) as $field_id => $field) {
            if (Caldera_Forms_Fields::not_support(Caldera_Forms_Field_Util::get_type($field, $this->form), 'entry_list')) {
                continue;
            }
            $this->fields[$field_id] = [
                'ID' => caldera_forms_very_safe_string($field_id),
                'name' => isset($field['label']) ? caldera_forms_very_safe_string($field['label']) : '',
                'type' => Caldera_Forms_Field_Util::get_type($field, $this->form)
            ];
        }
    }


}