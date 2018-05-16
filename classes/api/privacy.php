<?php


class Caldera_Forms_API_Privacy extends Caldera_Forms_API_Form
{


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


    public function set_pii_fields( $pii_fields ){
        foreach( $this->get_fields() as $field ){
            $this->form[ 'fields' ][ $field[ 'ID' ] ][ 'config' ][ Caldera_Forms_Field_Util::CONFIG_PERSONAL] = (int) in_array( $field[ 'ID' ], $pii_fields );
        }
        return $this;
    }

    public function set_email_identifying_fields( $email_fields )
    {
        foreach( $this->get_fields() as $field ){
            $this->form[ 'fields' ][ $field[ 'ID' ] ][ 'config' ][ Caldera_Forms_Field_Util::CONFIG_EMAIL_IDENTIFIER ] = (int) in_array( $field[ 'ID' ], $email_fields );
        }
        return $this;
    }
}