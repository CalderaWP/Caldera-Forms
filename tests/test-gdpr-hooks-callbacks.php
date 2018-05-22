<?php

class Test_Caldera_Forms_GDPR extends Caldera_Forms_Test_Case
{


    public function testEnableForms()
    {
        $form_export_enabled_id = $this->import_contact_form();
        $form_export_enabled = Caldera_Forms_Forms::get_form($form_export_enabled_id);
        $form_export_enabled = Caldera_Forms_Forms::update_privacy_export_enabled( $form_export_enabled, true );
        Caldera_Forms_Forms::save_form( $form_export_enabled );
        $form_export_not_enabled_id = $this->import_autoresponder_form();

        $enabled_forms = Caldera_Forms_GDPR::enabled_forms();
        $this->assertTrue( is_array( $enabled_forms ) );
        $this->assertTrue( in_array( $form_export_enabled_id, $enabled_forms ) );
        $this->assertFalse( in_array( $form_export_not_enabled_id, $enabled_forms ) );
    }


}