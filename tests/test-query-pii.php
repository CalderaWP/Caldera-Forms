<?php

/**
 * Class Test_Caldera_Forms_Query_Paginated
 *
 * @covers Caldera_Forms_Query_Paginated
 */
class Test_Caldera_Forms_Query_Pii extends Caldera_Forms_Test_Case {



    /**
     *  Test that paginated results have the right results
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Query_Pii::select_by_entry_ids()
     */
    public function testPagination(){
        $email_field = 'email_address';
        $email = 'Roy@hiRoy.club';
        $form_id = $this->import_contact_form();
        $form_id_two = $this->import_autoresponder_form();
        $entry_ids = [];
        for( $i = 0; $i <=10; $i++) {
            //Entries that should show in PII lookup by $email
            $entries = $this->save_identifiable_entries_for_two_forms($form_id, $form_id, $email, $email_field);
            //Same form, different email
            $this->save_identifiable_entries_for_two_forms($form_id, $form_id, 'Mike@hiRoy.club', $email_field);
            //Same email, different form
            $this->save_identifiable_entries_for_two_forms($form_id_two, $form_id_two, $email, $email_field);
            $entry_ids = array_merge($entry_ids, $entries['form_1']);
        }

        $form = Caldera_Forms_Forms::get_form($form_id);
        $privacy = new Caldera_Forms_API_Privacy($form);
        $pii_fields = [
            'fld_8768091',
            'fld_9970286',
        ];
        $email_fields = ['fld_6009157'];
        $privacy->set_pii_fields($pii_fields);
        $privacy->set_email_identifying_fields( $email_fields );
        $privacy->save_form();
        $form = Caldera_Forms_Forms::get_form( $form_id );
        $this->assertSame( $email_fields, Caldera_Forms_Forms::email_identifying_fields( $form,true  ) );
        $this->assertSame( $pii_fields, Caldera_Forms_Forms::personally_identifying_fields( $form,true  ) );

        $query = new Caldera_Forms_Query_Pii(
                $form,
                $email,
                new Caldera_Forms_Query_Paginated($form),
                15
        );
        $results_one = $query->get_page(1);
        $this->assertSame( 15, $results_one->count() );
        $results_two = $query->get_page(2);
        $this->assertSame( 15, $results_two->count() );

    }






}