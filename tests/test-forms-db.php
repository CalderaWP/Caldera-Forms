<?php

class Test_Caldera_Forms_DB extends Caldera_Forms_Test_Case
{

    /**
     * Test forms list without details
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_DB_Form::get_all()
     * @covers Caldera_Forms_DB_Form::prepare_found()
     */
    public function testGetPrimaryForms()
    {
        $form_one_id = $this->import_autoresponder_form();
        $form_two_id = $this->import_contact_form();
        $forms = Caldera_Forms_DB_Form::get_instance()->get_all();
        $this->assertCount(2, $forms);
        $found_one = $found_two = false;
        foreach ( $forms as $form ){
            $this->assertArrayHasKey( 'form_id', $form );
            $this->assertArrayHasKey( 'config', $form );
            $this->assertArrayHasKey( 'type', $form );
            $this->assertArrayHasKey( 'id', $form );
            if( $form_one_id === $form[ 'form_id' ] ){
                $found_one = true;
            }

            if( $form_two_id === $form[ 'form_id' ] ){
                $found_two = true;
            }

        }

        $this->assertTrue($found_one);
        $this->assertTrue($found_two);

        $form_three_id = $this->import_contact_form(false);
        $forms = Caldera_Forms_DB_Form::get_instance()->get_all();
        $this->assertCount(3, $forms);
        $found_one = $found_two = $found_three = false;
        foreach ( $forms as $form ){
            $this->assertArrayHasKey( 'form_id', $form );
            $this->assertArrayHasKey( 'config', $form );
            $this->assertArrayHasKey( 'type', $form );
            $this->assertArrayHasKey( 'id', $form );
            if( $form_one_id === $form[ 'form_id' ] ){
                $found_one = true;
            }

            if( $form_two_id === $form[ 'form_id' ] ){
                $found_two = true;
            }

            if( $form_three_id  === $form[ 'form_id'] ){
                $found_three = true;
            }

        }

        $this->assertTrue($found_one);
        $this->assertTrue($found_two);
        $this->assertTrue($found_three );

        //create revisions to forms
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_two_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_three_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        //count should still be three
        $forms = Caldera_Forms_DB_Form::get_instance()->get_all();
        $this->assertCount(3, $forms);
    }


    /**
     * Test that revisions are not returned
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_DB_Form::get_all()
     * @covers Caldera_Forms_DB_Form::update()
     * @covers Caldera_Forms_DB_Form::update_type()
     * @covers Caldera_Forms_Forms::save_to_db()
     */
    public function testGetWithoutRevisions()
    {
        $form_one_id = $this->import_autoresponder_form();
        $form_two_id = $this->import_contact_form();

        //create revisions to forms
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_two_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        //count should still be two
        $forms = Caldera_Forms_DB_Form::get_instance()->get_all();
        $this->assertCount(2, $forms);

    }

    /**
     * Test that only revisions are returned when all forms are requested
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_DB_Form::get_all()
     * @covers Caldera_Forms_DB_Form::update()
     * @covers Caldera_Forms_DB_Form::update_type()
     * @covers Caldera_Forms_Forms::save_to_db()
     * @covers Caldera_Forms_DB_Form::create()
     */
    public function testGetAllRevisions()
    {
        $form_one_id = $this->import_autoresponder_form();
        $form_two_id = $this->import_contact_form();
        //create revisions to forms
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_two_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        Caldera_Forms_Forms::save_form( Caldera_Forms_Forms::get_form($form_one_id));
        $forms = Caldera_Forms_DB_Form::get_instance()->get_all( 'false');
        $this->assertCount(6, $forms);
        foreach( $forms as $form ){
            $this->assertArrayHasKey( 'type', $form );
            $this->assertSame( 'revision', $form[ 'type'] );
        }

    }



}