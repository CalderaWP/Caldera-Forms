<?php

/**
 * Coverage for auto-responder processor
 *
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 */
class Test_Auto_Responder extends Caldera_Forms_Mailer_Test_Case{



    /**
     * Test that the contact form import utility works
     *
     * @since 1.5.9
     *
     * @group form
     * @group email
     * @group autoresponder
     * @group processors
     *
     * @covers Caldera_Forms_Forms::import_form()
     * @covers Caldera_Forms_Test_Case::import_contact_form()
     * @covers Caldera_Forms_Test_Case::recursive_cast_array()
     */
    public function test_contact_form_import(){
        $form_id = $this->import_contact_form(false);
        $form = Caldera_Forms_Forms::get_form($form_id);
        $this->assertSame($form_id, $form['ID']);

        $this->assertArrayHasKey('fields', $form);
        $this->assertArrayHasKey('layout_grid', $form);
        $this->assertArrayHasKey('pinned', $form);
        $this->assertArrayHasKey('fields', $form);
        $this->assertArrayHasKey('fld_8768091', $form['fields']);
        $this->assertArrayHasKey('slug', $form['fields']['fld_8768091']);
        $this->assertEquals('first_name', $form['fields']['fld_8768091']['slug']);
        $this->assertArrayHasKey('fld_9970286', $form['fields']);
        $this->assertArrayHasKey('config', $form['fields']['fld_9970286']);
        $this->assertTrue(is_array($form['fields']['fld_9970286']['config']));

        $this->assertArrayHasKey('processors', $form);
        $this->assertArrayHasKey('fp_64814225', $form['processors']);
        $this->assertArrayHasKey('config', $form['processors']['fp_64814225']);
        $this->assertEquals( 'FROM NAME', $form['processors']['fp_64814225'][ 'config'][ 'sender_name' ] );
        $this->assertEquals( 'cc1@auto.com, %email_address%, cc3@auto.com', $form['processors']['fp_64814225'][ 'config'][ 'cc' ] );
    }

    /**
     * Test that the to setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     *
     * @covers Test_Main_Mailer:submit_contact_form()
     * @covers Test_Main_Mailer:mailer_callback()
     */
    public function test_capture_entry_id(){
        $this->submit_contact_form(false);
        $this->assertTrue(is_numeric($this->entry_id));
    }

    /**
     * Test that "caldera_forms_do_autoresponse" action fired
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     *
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_sent(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertEquals( 0, did_action( 'caldera_forms_autoresponder_failed' ) );

        $this->assertEquals( 1, did_action( 'caldera_forms_do_autoresponse' )  );
    }

    /**
     * Test that the to setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     *
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_to(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertEquals('auto@example.com', $mailer->get_recipient('to')->address);
        $expected = 'Mike <auto@example.com>';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);

    }

    /**
     * Test that the FROM setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     * 
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_from(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $expected = 'From: FROM NAME <from@auto.com>';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);
    }

    /**
     * Test that the REPLYTO setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     *
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_replyto(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $expected = 'Reply-To: replyto@auto.com';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);
    }

    /**
     * Test that the BCC setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     *
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_bcc(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $expected = 'Bcc: bc1@auto.com';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);

    }

    /**
     * Test that the content of the email is correct
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     *
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_content(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();

        $this->assertEquals('a2c36b848220ac8c84e8450a0b6ba420', md5($mailer->get_sent()->body));
    }



}

