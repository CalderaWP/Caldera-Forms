<?php

/**
 * Coverage for main mailer
 *
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 */
class Test_Main_Mailer extends Caldera_Forms_Mailer_Test_Case{


    /**
     * Test that the contact form import utility works
     *
     * @since 1.5.9
     *
     * @group form
     * @group email
     * @group mainmailer
     * 
     * @covers Caldera_Forms_Forms::import_form()
     * @covers Caldera_Forms_Test_Case::import_contact_form()
     * @covers Caldera_Forms_Test_Case::recursive_cast_array()
     */
    public function test_contact_form_import(){
        $form_id = $this->import_contact_form();
        $form = Caldera_Forms_Forms::get_form($form_id);
        $this->assertSame($form_id, $form['ID']);

        $this->assertArrayHasKey('fields', $form);
        $this->assertArrayHasKey('layout_grid', $form);
        $this->assertArrayHasKey('pinned', $form);
        $this->assertArrayHasKey('fields', $form);
        $this->assertArrayHasKey('fld_29462', $form['fields']);
        $this->assertArrayHasKey('slug', $form['fields']['fld_29462']);
        $this->assertEquals('header', $form['fields']['fld_29462']['slug']);
        $this->assertArrayHasKey('fld_8768091', $form['fields']);
        $this->assertArrayHasKey('config', $form['fields']['fld_29462']);
        $this->assertTrue(is_array($form['fields']['fld_29462']['config']));
    }

    /**
     * Test that "caldera_forms_mailer_failed" action fired
     *
     * @since 1.5.9
     *
     * @group email
     * @group mainmailer
     * 
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_sent(){
        $this->submit_contact_form(true);
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertEquals( 0, did_action( 'caldera_forms_mailer_failed' ) );
        $this->assertEquals( 1, did_action( 'caldera_forms_mailer_complete' ) );
    }

    /**
     * Test that the to setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group mainmailer
     * 
     * @covers Test_Main_Mailer:submit_contact_form()
     * @covers Test_Main_Mailer:mailer_callback()
     */
    public function test_capture_entry_id(){
        $this->submit_contact_form();
        $this->assertTrue(is_numeric($this->entry_id));
    }

    /**
     * Test that the to setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group mainmailer
     * @group to
     * 
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_to(){
        $this->submit_contact_form();
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertEquals('to@example.com', $mailer->get_recipient('to')->address);

        //The second value here is a magic tag value based on mock submission data
        $expected = 'To: to@example.com, roy@roysivan.com';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);

        $this->assertEquals('to@example.com', $mailer->get_recipient('to', 0, 0 )->address);
        $this->assertEquals('roy@roysivan.com', $mailer->get_recipient('to', 0, 1 )->address);


    }

    /**
     * Test that the FROM setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group mainmailer
     * @group from
     * 
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_from(){
        $this->submit_contact_form();
        $mailer = tests_retrieve_phpmailer_instance();
        $expected = 'From: Caldera Forms Notification <from@from.com>';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);
    }

    /**
     * Test that the REPLYTO setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group mainmailer
     * @group replyto
     * 
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_replyto(){
        $this->submit_contact_form();
        $mailer = tests_retrieve_phpmailer_instance();
        $expected = 'Reply-To: roy@roysivan.com';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);
    }

    /**
     * Test that the BCC setting of main email is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group mainmailer
     * @group bcc
     * 
     * @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_bcc(){
        $this->submit_contact_form();
        $mailer = tests_retrieve_phpmailer_instance();
        $expected = 'bcc1@example.com, bcc2@example2.com';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);
        $this->assertSame('bcc1@example.com', $mailer->get_recipient('bcc', 0, 0)->address );
        $this->assertSame('bcc2@example2.com', $mailer->get_recipient('bcc', 0, 1)->address );
    }

    /**
     * Test that the content of the email is correct
     *
     * @since 1.5.9
     *
     * @group email
     * @group mainmailer
     * @group mainmailer
     *
     *  @covers Caldera_Forms_Save_Final::do_mailer()
     */
    public function test_content(){
        $this->submit_contact_form();
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertEquals('97cae2f8e53fc89902562dac3714475b', md5($mailer->get_sent()->body));
    }

}

