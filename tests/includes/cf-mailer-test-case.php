<?php

abstract class Caldera_Forms_Mailer_Test_Case extends Caldera_Forms_Test_Case {
    use \calderawp\calderaforms\Tests\Util\Traits\TestsWpMail;
    /** @inheritdoc */
    public function tearDown(){
        $this->reset();
        parent::tearDown();
    }

    /** @inheritdoc */
    public function setUp(){
        $this->reset();
        parent::setUp();
    }

    /**
     * ID of last form submitted
     *
     * @since 1.5.9
     *
     * @var string
     */
    protected $form_id;

    /**
     * ID of last entry submitted
     *
     * @since 1.5.9
     *
     * @var integer
     */
    protected $entry_id;

    /**
     * Configuration of last form submitted
     *
     * @since 1.5.9
     *
     * @var array
     */
    protected $form;

    /**
     * Submission data of last submission
     *
     * @since 1.5.9
     *
     * @var array
     */
    protected $submission_data;

    /**
     * Submit the contact form
     *
     * @param bool $main_mailer Optional. If true, the default, contact form for main mailer is used. If false, contact form for auto-responder is used.
     * @param bool $skip_import Optional. If true, you must import form and set form and form_id props first. Default is false.
     * @since 1.5.9
     */
    protected function submit_contact_form($main_mailer = true, $skip_import = false ){
        if ( false === $skip_import ) {
            //setup submit data and class properties we need for assertions
            $this->form_id = $this->import_contact_form($main_mailer);
            $this->form = Caldera_Forms_Forms::get_form($this->form_id);
        }
        $data = array(
            'fld_8768091' => 'Roy',
            'fld_9970286' => 'Sivan',
            'fld_6009157' => 'roy@roysivan.com',
            'fld_7683514' => 'Hi Roy',
            'fld_7908577' => 'click',
        );
        if( ! $main_mailer ){
            $data = array (
                '_cf_frm_ct' => '1',
                '_cf_cr_pst' => '4',
                'twitter' => '',
                'fld_8768091' => 'Mike',
                'fld_9970286' => 'Corkum',
                'fld_6009157' => 'roy@roysivan.com',
                'fld_7683514' => 'Triangular',
                'fld_7908577' => 'click',
                'formId' => $this->form_id,
                'instance' => '1',
                'postDisable' => '0',
                'target' => '#caldera_notices_1',
                'loadClass' => 'cf_processing',
                'loadElement' => '_parent',
                'hiderows' => 'true',
                'action' => 'cf_process_ajax_submit',
            );
            add_filter( 'caldera_forms_send_email', '__return_false', 10000 );
            add_filter( 'caldera_forms_autoresponse_mail', array( $this, 'auto_callback' ), 51, 4);
        }else{
            //hook into mailer filter
            add_filter('caldera_forms_mailer', array($this, 'mailer_callback'), 51, 4);
        }

        $this->submission_data = $this->submission_data($this->form_id,$data);
        //Set up super globals
        $_POST = $this->submission_data;
        $_SERVER['HTTP_REFERER'] = $this->submission_data['_wp_http_referer'];
        //prevent ajax redirect
        remove_action('caldera_forms_redirect', 'cf_ajax_redirect', 10);
        //prevent Caldera Forms from exiting PHP session
        add_filter('caldera_forms_redirect_url_complete', '__return_null', 1000);

        //submit form
        Caldera_Forms::process_submission();
    }


    /**
     * Hook into caldera_forms_mailer to capture last entry ID
     *
     * @uses "caldera_forms_mailer" filter
     *
     * @param $mail
     * @param $data
     * @param $form
     * @param $entryid
     * @return array
     */
    public function mailer_callback($mail, $data, $form, $entryid) {
        $this->entry_id = $entryid;
        return $mail;
    }

    public function auto_callback( $email_message, $config, $form, $entry_id){
        $this->entry_id = $entry_id;
        return $email_message;
    }

    /**
     * Reset test setup
     *
     * @since 1.6.0
     *
     * Nulls all properties and resets mock phpmailer
     */
    protected function reset(){
        $this->entry_id = null;
        $this->form_id = null;
        $this->form = null;
        $this->submission_data = null;
        reset_phpmailer_instance();
    }


    /**
     * Test that the contact form import utility works
     *
     * @since 1.6.0
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


}
