<?php

/**
 * Class Test_Field_Util_Defaults
 *
 * Test for methods of the Caldera_Forms_Field_Util that deal with field defaults
 */
class Test_Field_Util_Defaults extends Caldera_Forms_Test_Case
{
    /**
     * The ID of the field we are testing
     *
     * @since 1.6.0
     */
    const MOCK_FIELD_ID = 'fld_9641225';

    /**
     * The ID of the form we are testing
     *
     * @since 1.6.0
     */
    const MOCK_FORM_ID = 'CF5aa6b25bf0f72';

    /**
     * The name of the form we are testing
     *
     * @since 1.6.0
     */
    const MOCK_FORM_NAME = 'Field Utility Defaults Githubn #2325';

    /**
     * The option IDs for the checkbox field we are testing
     *
     * @since 1.6.0
     *
     * @var array
     */
    protected $optIds = array(
        'opt1673144',
        'opt1483039'
    );

    /**
     * The options for the checkbox field we are testing
     *
     * @since 1.6.0
     *
     * @var array
     */
    protected $options;

    /**
     * The mock form
     *
     * @since 1.6.0
     *
     * @var array
     */
    protected $form;

    /**
     * Reset the mock form before tests
     */
    public function setUp(){
        $this->reset_mock_form();
        parent::setUp();
    }

    /**
     * Test the method that sets is used to determined the checked HTML attribute in checkbox fields
     *
     * @since 1.6.0
     *
     * @group fields
     * @group checkbox
     * @group calculation
     *
     * @covers Caldera_Forms_Field_Util::is_checked_option()
     */
    public function test_is_selected_option(){
        $this->assertTrue( Caldera_Forms_Field_Util::is_checked_option( 'yes', array(
            'yes',
            'no'
        )));

        $this->assertFalse( Caldera_Forms_Field_Util::is_checked_option( 'maybe', array(
            'yes',
            'no'
        )));

        $this->assertFalse( Caldera_Forms_Field_Util::is_checked_option( '', array('yes', 'no')));
        $this->assertFalse( Caldera_Forms_Field_Util::is_checked_option( 'maybe', array()));
        $this->assertFalse( Caldera_Forms_Field_Util::is_checked_option( '', array()));
    }

    /**
     * Test calculation values
     *
     * @since 1.6.0
     *
     * @group fields
     * @group checkbox
     * @group calculation
     *
     * @covers Caldera_Forms_Field_Util::get_default_calc_value()
     */
    public function test_calc_default(){
        $form = $this->reset_mock_form( '1' );
        //No calc_value, but value is 1
        $this->assertSame( 1,
            (int) Caldera_Forms_Field_Util::get_default_calc_value(
                Caldera_Forms_Field_Util::get_field( self::MOCK_FIELD_ID, $this->form ),
                $this->form
            )
        );

        //calc_value is 2
        $this->reset_mock_form( '2' );
        $this->assertSame( 2,
            (int) Caldera_Forms_Field_Util::get_default_calc_value(
                Caldera_Forms_Field_Util::get_field( self::MOCK_FIELD_ID, $this->form ),
                $this->form
            )
        );

        //Should be 1 + 2 = 3
        $this->reset_mock_form( array( '1', '2') );
        $this->assertSame( 3,
            (int) Caldera_Forms_Field_Util::get_default_calc_value(
                Caldera_Forms_Field_Util::get_field( self::MOCK_FIELD_ID, $this->form ),
                $this->form
            )
        );
    }

    /**
     * Check we can filter the field default, as we would to set multiple checkbox defaults.
     *
     * @since 1.6.0
     *
     * @group fields
     * @group default
     * @group checkbox

     * @covers Caldera_Forms_Field_Util::get_field()
     * @covers Caldera_Forms_Field_Util::apply_field_filters()
     */
    public function test_check_default_filter(){
        add_filter( 'caldera_forms_render_get_field', function( $field  ){
            if( self::MOCK_FIELD_ID === $field[ 'ID' ] ){
                $field[ 'config' ][ 'default' ] = array(
                    'Yes', 'No'
                );


            }

            return $field;
        }, 10, 2 );

        $form = $this->reset_mock_form();
        $filtered_field = Caldera_Forms_Field_Util::get_field( self::MOCK_FIELD_ID, $form, true );
        $this->assertSame( array(
            'Yes', 'No'
        ), $filtered_field[ 'config' ][ 'default' ] );

        $form = $this->reset_mock_form();
        $filtered_field = Caldera_Forms_Field_Util::get_field( self::MOCK_FIELD_ID, $form, false );
        $this->assertSame( '', $filtered_field[ 'config' ][ 'default' ] );
    }


    /**
     * Reset the mock form
     *
     * @since 1.6.0
     *
     * @param string|array $defaults Defaults for checkbox field
     * @return array
     */
    protected function reset_mock_form($defaults = ''){


        $this->options = array(
            $this->optIds[0] =>
                array(
                    'calc_value' => '',
                    'value' => '1',
                    'label' => 'Yes',
                ),
            $this->optIds[1] =>
                array(
                    'calc_value' => 2,
                    'value' => '2',
                    'label' => 'No',
                ),
        );

        $this->form = array(
            '_last_updated' => 'Mon, 12 Mar 2018 17:55:43 +0000',
            'ID' => self::MOCK_FORM_ID,
            'cf_version' => '1.6.0.beta2',
            'name' => self::MOCK_FORM_NAME,
            'scroll_top' => 0,
            'success' => 'Form has been successfully submitted. Thank you.			',
            'db_support' => 1,
            'pinned' => 0,
            'hide_form' => 1,
            'check_honey' => 1,
            'avatar_field' => null,
            'form_ajax' => 1,
            'custom_callback' => '',
            'layout_grid' =>
                array(
                    'fields' =>
                        array(
                            self::MOCK_FIELD_ID => '1:1',
                        ),
                    'structure' => '12',
                ),
            'fields' =>
                array(
                    self::MOCK_FIELD_ID =>
                        array(
                            'ID' => self::MOCK_FIELD_ID,
                            'type' => 'checkbox',
                            'label' => 'Checkbox',
                            'slug' => 'checkbox',
                            'conditions' =>
                                array(
                                    'type' => '',
                                ),
                            'caption' => '',
                            'config' =>
                                array(
                                    'custom_class' => '',
                                    'default_option' => '',
                                    'auto_type' => '',
                                    'taxonomy' => 'category',
                                    'post_type' => 'post',
                                    'value_field' => 'name',
                                    'orderby_tax' => 'count',
                                    'orderby_post' => 'ID',
                                    'order' => 'ASC',
                                    'default' => $defaults,
                                    'option' => $this->options
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
                    'sender_email' => 'caldera-dev@noms.com',
                    'reply_to' => '',
                    'email_type' => 'html',
                    'recipients' => '',
                    'bcc_to' => '',
                    'email_subject' => '2325 ',
                    'email_message' => '{summary}',
                ),
            'antispam' =>
                array(
                    'sender_name' => '',
                    'sender_email' => '',
                ),
            'conditional_groups' =>
                array(),
            'settings' =>
                array(
                    'responsive' =>
                        array(
                            'break_point' => 'sm',
                        ),
                ),
            'version' => '1.6.0.beta2',
            'db_id' => '632',
            'type' => 'primary',
        );

        global $form;
        $form = $this->form;
        return $this->form;
    }

}