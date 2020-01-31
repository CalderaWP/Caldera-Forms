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
     * Test that the to setting of auto-responder is set properly
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
     * @covers Caldera_Forms_Save_Final::send_auto_response()
     */
    public function test_sent(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertEquals( 0, did_action( 'caldera_forms_autoresponder_failed' ) );
        $this->assertEquals( 1, did_action( 'caldera_forms_do_autoresponse' )  );

    }

    /**
     * Test that the to setting of auto-responder is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     * @group to
     *
     * @covers Caldera_Forms_Save_Final::send_auto_response()
     */
    public function test_to(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $expected = 'Mike <auto@example.com>';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);
        $this->assertEquals('auto@example.com', $mailer->get_recipient('to')->address);
        $this->assertSame('Mike', $mailer->get_recipient('to')->name );

    }

    /**
     * Test that the FROM setting of auto-responder is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     * @group from
     * 
     * @covers Caldera_Forms_Save_Final::send_auto_response()
     */
    public function test_from(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $expected = 'From: FROM NAME <from@auto.com>';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);


    }

    /**
     * Test that the REPLYTO setting of auto-responder is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     * @group replyto
     *
     * @covers Caldera_Forms_Save_Final::send_auto_response()
     */
    public function test_replyto(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $expected = 'Reply-To: replyto@auto.com';
        $this->assertTrue(strpos($mailer->get_sent()->header, $expected) > 0);
    }

    /**
     * Test that the BCC setting of auto-responder is set properly
     *
     * @since 1.5.9
     *
     * @group email
     * @group autoresponder
     * @group processors
     * @group bcc
     *
     * @covers Caldera_Forms_Save_Final::send_auto_response()
     */
    public function test_bcc(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertSame('bc1@auto.com', $mailer->get_recipient('bcc', 0, 0)->address );

    }



    /**
     * Test that the BCC setting of auto-responder is set properly when there are multiple BCCs
     *
     * @since 1.6.0
     *
     * @group email
     * @group autoresponder
     * @group processors
     * @group form
     * @group bcc
     *
     * @covers Caldera_Forms_Save_Final::send_auto_response()
     */
    public function test_multi_bcc(){
        //import test form (only has one bcc)
        $json = file_get_contents($this->get_path_for_auto_responder_contact_form_import());
        $config = $this->recursive_cast_array(json_decode($json));
        $config[ 'ID' ] = uniqid('CF' );
        $form_id = Caldera_Forms_Forms::import_form($config);
        $this->form_id = $form_id;

        //change bccs to two
        $bccs = 'mbc1@example.com, mbc2@example.com';
        $config[ 'processors' ][ 'fp_64814225' ][ 'config' ][ 'bcc' ] = $bccs;
        $updated = Caldera_Forms_Forms::save_form( $config );
        //Make sure save worked and ID didn't change
        $this->assertTrue( is_string( $updated ) );
        $this->assertSame( $updated, $this->form_id );

        $this->form = Caldera_Forms_Forms::get_form($this->form_id);
        $this->assertSame( 'Contact Form With Auto Responder', $this->form['name' ] );
        //Make sure update worked
        $this->assertSame( $this->form[ 'ID' ], $this->form_id );
        $this->assertSame(
            $config[ 'processors' ][ 'fp_64814225' ][ 'config' ][ 'bcc' ],
            $this->form[ 'processors' ][ 'fp_64814225' ][ 'config' ][ 'bcc' ]
        );


        $this->submit_contact_form(false,true );

        //make sure form sent -- other sends we trust because of this::test_sent() but this is a different way
        $this->assertEquals( 1, did_action( 'caldera_forms_do_autoresponse' )  );
        $this->assertEquals( 0, did_action( 'caldera_forms_autoresponder_failed' ) );

        //Actual test
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertSame('mbc1@example.com', $mailer->get_recipient('bcc', 0, 0)->address );
        $this->assertSame('mbc2@example.com', $mailer->get_recipient('bcc', 0, 1)->address );

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
     * @covers Caldera_Forms_Save_Final::send_auto_response()
     */
    public function test_content(){
        $this->submit_contact_form(false);
        $mailer = tests_retrieve_phpmailer_instance();

        $this->assertEquals('a2c36b848220ac8c84e8450a0b6ba420', md5($mailer->get_sent()->body));
    }



}

