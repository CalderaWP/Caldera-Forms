<?php
/**
 * Base test case class
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class Caldera_Forms_Test_Case extends WP_UnitTestCase {

	const MOCK_FORM_ID = 'cf12345hiroy';

    /**
     * A form that isn't saved or on filter to use as a mock
     *
     * @since 1.3.4
     *
     * @var array
     */
    protected $mock_form;

    public function setUp(){
		$this->set_mock_form();
		//test that mock form is set properly
		$this->assertSame( self::MOCK_FORM_ID, $this->mock_form[ 'ID' ] );
		$this->assertSame( 1, $this->mock_form[ 'pinned' ] );
		parent::setUp();
	}

	public function tearDown(){

		$forms = Caldera_Forms_Forms::get_forms();

		if( ! empty( $forms  ) ){
			foreach( $forms  as $id => $form) {
				Caldera_Forms_Forms::delete_form( $id );
			}
		}

		wp_cache_delete( '_caldera_forms_forms', 'options' );
		parent::tearDown();
        reset_phpmailer_instance();
	}

	/**
	 * Forms setup using filter
	 *
	 * @since 1.3.4
	 *
	 * @var array
	 */
	protected $forms_on_filters = array(
		'simple-form-with-just-a-text-field',
		'contact-form'
	);

	/**
	 * Get a form saved in file to use as a mock
	 *
	 * @since 1.3.4
	 *
	 * @param string $name Name of form
	 * @param bool|true $add_external_flag Optional. Add _external_form key. Default is true
	 *
	 * @return bool
	 */
	protected function get_file_mock( $name, $add_external_flag = true ){
		if( in_array( $name, $this->forms_on_filters ) ){
			$name = str_replace( '-', '_', $name );
			$cb = 'caldera_forms_tests_get_' . $name;
			$form =  call_user_func( $cb, array() );
			if( $add_external_flag ){
				$form[ '_external_form' ] = true;
			}

			return $form;
		}
	}

    /**
     * Set mock_form property
     *
     * @since 1.5.7
     */
	private function set_mock_form(){
        $this->mock_form = array(
            'ID'                 => self::MOCK_FORM_ID,
            'name'               => 'Another form',
            'description'        => '',
            'db_support'         => 1,
            'pinned'             => 1,
            'hide_form'          => 1,
            'check_honey'        => 1,
            'success'            => __( 'Form has been successfully submitted. Thank you.', 'caldera-forms' ),
            'avatar_field'       => null,
            'form_ajax'          => 1,
            'custom_callback'    => '',
            'layout_grid'        =>
                array(
                    'fields'    =>
                        array(
                            'fld_1724450' => '1:1',
                            'fld_6125005' => '1:2',
                            'fld_7269029' => '2:1',
                            'fld_7896909' => '3:1',
                        ),
                    'structure' => '6:6|12#12',
                ),
            'fields'             =>
                array(
                    'fld_1724450' =>
                        array(
                            'ID'         => 'fld_1724450',
                            'type'       => 'text',
                            'label'      => 'Text',
                            'slug'       => 'text',
                            'conditions' =>
                                array(
                                    'type' => '',
                                ),
                            'caption'    => '',
                            'config'     =>
                                array(
                                    'custom_class'  => '',
                                    'placeholder'   => '',
                                    'default'       => '',
                                    'mask'          => '',
                                    'type_override' => 'text',
                                ),
                        ),
                    'fld_6125005' =>
                        array(
                            'ID'         => 'fld_6125005',
                            'type'       => 'email',
                            'label'      => 'Email',
                            'slug'       => 'email',
                            'conditions' =>
                                array(
                                    'type' => '',
                                ),
                            'required'   => 1,
                            'caption'    => 'Make emails',
                            'entry_list' => 1,
                            'config'     =>
                                array(
                                    'custom_class' => '',
                                    'placeholder'  => '',
                                    'default'      => '',
                                ),
                        ),
                    'fld_7269029' =>
                        array(
                            'ID'         => 'fld_7269029',
                            'type'       => 'button',
                            'label'      => 'Next Page',
                            'slug'       => 'next_page',
                            'conditions' =>
                                array(
                                    'type' => '',
                                ),
                            'caption'    => '',
                            'config'     =>
                                array(
                                    'custom_class' => '',
                                    'type'         => 'next',
                                    'class'        => 'btn btn-default',
                                    'target'       => '',
                                ),
                        ),
                    'fld_7896909' =>
                        array(
                            'ID'         => 'fld_7896909',
                            'type'       => 'button',
                            'label'      => 'Submit',
                            'slug'       => 'submit',
                            'conditions' =>
                                array(
                                    'type' => '',
                                ),
                            'caption'    => '',
                            'config'     =>
                                array(
                                    'custom_class' => '',
                                    'type'         => 'submit',
                                    'class'        => 'btn btn-default',
                                    'target'       => '',
                                ),
                        ),
                ),
            'page_names'         =>
                array(
                    0 => 'Page 1',
                    1 => 'Page 2',
                ),
            'conditional_groups' =>
                array(
                    'fields' =>
                        array(),
                ),
            'settings'           =>
                array(
                    'responsive' =>
                        array(
                            'break_point' => 'sm',
                        ),
                ),
            'mailer'             =>
                array(
                    'on_insert'     => 1,
                    'sender_name'   => 'Caldera Forms Notification',
                    'sender_email'  => 'admin@local.dev',
                    'reply_to'      => '',
                    'email_type'    => 'html',
                    'recipients'    => '',
                    'bcc_to'        => '',
                    'email_subject' => 'Another form',
                    'email_message' => '{summary}',
                ),
        );
    }

    /**
     * Create a mock entry to test
     *
     * @since 1.4.0
     */
    protected function create_entry( array $form = null ){
        if ( ! $form ) {
            $form = $this->mock_form;
        }
        $data = array();
        $i = 0;
        foreach( $form[ 'fields' ] as $field_id => $field_config ){
            if ( 1 == $i ) {
                $data[ $field_id ] = $field_id . '_' . rand();
            } else {
                $data[ $field_id ] = array(
                    rand(),
                    5 => rand(), rand(), 'batman'
                );
            }
            if( 0 == $i ){
                $i = 1;
            }else{
                $i = 0;
            }
        }

        $entry_id = Caldera_Forms_Save_Final::create_entry( $form, $data  );
        return array(
            'id' => $entry_id,
            'field_data' => $data,
            'form_id' => $form[ 'ID' ],
        );
    }

    /**
     * Import contact form without auto-responder
     *
     * @since 1.5.9
     *
     * @param bool $main_mailer Optional. If true, the default, contact form for main mailer is imported. If false, contact form for auti-responder is imported.
     * @return string
     */
    protected function import_contact_form($main_mailer = true ){
        if ($main_mailer) {
            $file = dirname(__FILE__) . '/forms/contact-forms-no-auto-responder.json';
        } else {
            $file = dirname(__FILE__) . '/forms/contact-form-autoresponder.json';

        }

        return $this->import_form($file);
    }



    /**
     * Cast array or object, like a form import, to array
     *
     * @since 1.5.9
     *
     * @param $array_or_object
     * @return array
     */
    protected function recursive_cast_array( $array_or_object ){
        $array_or_object = (array) $array_or_object;
        foreach ( $array_or_object as $key => $value ){
            if( is_array( $value ) || is_object( $value ) ){
                $array_or_object[ $key ] = $this->recursive_cast_array( $value );
            }

        }
        return $array_or_object;
    }

    /**
     * Create submission data for mock submissions.
     *
     * Designed to be used to set $_POST for contact form tests or other mock submission requiring tests.
     *
     * @since 1.5.9
     *
     * @param null|string $form_id
     * @param array $data
     * @return array
     */
    protected function submission_data( $form_id = null, array $data = array() ){
        if( ! $form_id ){
            $form_id = self::MOCK_FORM_ID;
        }

        $nonce = Caldera_Forms_Render_Nonce::create_verify_nonce( $form_id );


        $data = wp_parse_args( $data, array (
            '_cf_verify' => $nonce,
            '_wp_http_referer' => '/?page_id=4&preview=1&cf_preview=' . $form_id,
            '_cf_frm_id' => $form_id,
            '_cf_frm_ct' => '1',
            'cfajax' => $form_id,
            '_cf_cr_pst' => '4',
            'email' => '',
            'fld_8768091' => 'Roy',
            'fld_9970286' => 'Sivan',
            'fld_6009157' => 'roy@roysivan.com',
            'fld_7683514' => 'Hi Roy',
            'fld_7908577' => 'click',
            'formId' => $form_id,
            'instance' => '1',
            'request' => site_url("/cf-api/$form_id"),
            'postDisable' => '0',
            'target' => '#caldera_notices_1',
            'loadClass' => 'cf_processing',
            'loadElement' => '_parent',
            'hiderows' => 'true',
            'action' => 'cf_process_ajax_submit',
            'template' => '#cfajax_CF5a665b9ca5513-tmpl',
        ) );


        return $data;
    }

    /**
     * Import form by file path
     *
     * @since 1.5.9
     *
     * @param string $file Path to form config
     * @return string
     */
    protected function import_form($file) {
        $json = file_get_contents($file);
        $config = $this->recursive_cast_array(json_decode($json));
        $form_id = Caldera_Forms_Forms::import_form($config);
        return $form_id;
    }


}
