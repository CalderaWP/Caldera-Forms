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
	//Most utility functions are in traits so they can be included in other test suites.
	use Caldera_Forms_Has_Mock_Form, Caldera_Forms_Imports_Form, Caldera_Forms_Has_Data;

	const MOCK_FORM_ID = 'cf12345hiroy';


    /**
     * A form that isn't saved or on filter to use as a mock
     *
     * @since 1.3.4
     *
     * @var array
     */
    protected $mock_form;

    /** @inheritdoc */
    public function setUp(){
		$this->set_mock_form();
		$this->mock_form_id = self::MOCK_FORM_ID;
		//test that mock form is set properly
		$this->assertSame( self::MOCK_FORM_ID, $this->mock_form_id );
		$this->assertSame( self::MOCK_FORM_ID, $this->mock_form[ 'ID' ] );
		$this->assertSame( 1, $this->mock_form[ 'pinned' ] );
		parent::setUp();
	}

    /** @inheritdoc */
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
     * Assert a value is numeric
     *
     * @since 1.6
     *
     * @param mixed $maybeNumeric
     */
    protected function assertIsNumeric( $maybeNumeric, $message = '' ){
        $this->assertTrue( is_numeric( $maybeNumeric ), $message );
    }

}
