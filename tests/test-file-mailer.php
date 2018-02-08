<?php

/**
 * Coverage for file fields with emails
 *
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 */
class Test_File_Mailer extends Caldera_Forms_Mailer_Test_Case{

    /**
     * Unique ID used when rendering form
     *
     * @since 1.5.9
     *
     * @var string
     */
    protected $uniqid;

    /**
     * Import form for these tests
     *
     * @since 1.5.9
     *
     * @return string
     */
    protected function _import_form() {
        $file = dirname(__FILE__) . '/includes/forms/file.json';
        return $this->import_form($file);
    }

    /**
     * Test that the contact form import utility works
     *
     * @since 1.5.9
     *
     * @group file
     * @group email
     * @group form
     *
     * @covers Caldera_Forms_Forms::import_form()
     * @covers Caldera_Forms_Test_Case::_import_form()
     */
    public function test_form_import(){
        $form_id = $this->_import_form();
        $form = Caldera_Forms_Forms::get_form($form_id);
        $this->assertSame($form_id, $form['ID']);
        $this->assertSame('File Test', $form['name']);

        $this->assertArrayHasKey('fields', $form);
        $this->assertArrayHasKey('layout_grid', $form);
        $this->assertArrayHasKey('pinned', $form);
        $this->assertArrayHasKey('fields', $form);
        $this->assertArrayHasKey('fld_7469605', $form['fields']);
        $this->assertArrayHasKey('config', $form['fields']['fld_7469605']);
        $this->assertArrayHasKey('attach', $form['fields']['fld_7469605']['config']);
        $this->assertSame(1, (int)$form['fields']['fld_7469605']['config']['attach']);

    }

    /**
     * Set unique ID prop
     *
     * @since 1.5.9
     *
     * @param $uniqid
     */
    public function caldera_forms_file_uniqid($uniqid){
        $this->uniqid = $uniqid;
    }

    /**
     * Test that main mailer sent
     *
     * @since 1.5.9
     *
     * @group file
     * @group email
     */
    public function test_send(){
        $this->submit_form();
        $mailer = tests_retrieve_phpmailer_instance();
        $this->assertEquals( 0, did_action( 'caldera_forms_mailer_failed' ) );
        $this->assertEquals( 1, did_action( 'caldera_forms_mailer_complete' ) );

    }

    /**
     * Upload handler so we can have file field tests
     *
     * @since 1.5.9
     *
     * @group file
     * @group email
     *
     * @param $file
     * @param $args
     * @return array
     */
    public function upload_handler( $file, $args  ){
        $filename = dirname(__FILE__) . '/includes/josie.jpg';
        $tmp_name = wp_tempnam( $filename );

        copy( $filename, $tmp_name );

        $_FILES['fld_7469605'] = array(
            'tmp_name' => $tmp_name,
            'name'     => 'josie.jpg',
            'type'     => 'image/jpeg',
            'error'    => 0,
            'size'     => filesize( $file ),
        );

        $contents = file_get_contents($filename);
        $upload = wp_upload_bits(basename($filename), null, $contents);
        return $upload;

    }

    /**
     * Submit the form
     *
     * @since 1.5.9
     */
    protected function submit_form(){

        add_action( 'caldera_forms_file_uniqid', array( $this, 'caldera_forms_file_uniqid' ) );
        $this->form_id = $this->_import_form();

        $this->form = Caldera_Forms_Forms::get_form($this->form_id );
        Caldera_Forms::render_form( $this->form );

        /**
         * @var WP_Query
         */
        global $wp_query;
        $wp_query->query_vars[ 'cf_api' ] = $this->form_id;
        $this->assertFalse( is_null( $this->uniqid ) );
        $this->submission_data = $this->submission_data( $this->form_id, array (
            '_cf_frm_ct' => '1',
            '_cf_cr_pst' => '4',
            'email' => '',
            'instance' => '1',
            'postDisable' => '0',
            'target' => '#caldera_notices_1',
            'loadClass' => 'cf_processing',
            'loadElement' => '_parent',
            'hiderows' => 'true',
            'action' => 'cf_process_ajax_submit',

        ) );


        $this->submission_data[ 'fld_7469605' ] =  $this->uniqid;
        $this->submission_data[  'fld_3537903' ] = 'click';

        //Set up super globals
        $_POST = $this->submission_data;
        $_SERVER['HTTP_REFERER'] = $this->submission_data['_wp_http_referer'];
        //prevent ajax redirect
        remove_action('caldera_forms_redirect', 'cf_ajax_redirect', 10);
        //prevent Caldera Forms from exiting PHP session
        add_filter('caldera_forms_redirect_url_complete', '__return_null', 1000);

        //custom upload hander
        add_filter( 'caldera_forms_file_upload_handler', array( $this, 'upload_handler' ), 50, 2 );
        //submit form
        Caldera_Forms::process_submission();


    }



}

