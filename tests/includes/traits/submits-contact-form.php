<?php
trait Caldera_Forms_Submits_Contact_Form {
    use Caldera_Forms_Imports_Form;

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
}