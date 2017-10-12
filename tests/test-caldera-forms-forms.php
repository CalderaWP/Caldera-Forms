<?php
/**
 * Test the form config DB abstraction -- mainly in Caldera_Forms_Forms class
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Test_Caldera_Forms_Forms extends Caldera_Forms_Test_Case {


	/**
	 * Test getting a form config when form is declared in file system
	 *
	 * @since 1.3.4
	 *
	 * @group db
	 * @group db_form_config
	 *
	 * @covers Caldera_Forms_Forms::get_form
	 * @covers Caldera_Forms_Forms::is_internal_form
	 */
	public function test_get_form_from_file(){
		foreach ( $this->forms_on_filters as $form  ) {
			$mock      = $this->get_file_mock( $form );
			$from_file = Caldera_Forms_Forms::get_form( $form );
			$this->assertInternalType( 'array', $from_file );
			foreach ( $mock as $k => $v ) {
				$this->assertSame( $v, $from_file[ $k ] );
			}


			foreach ( $from_file as $k => $v ) {
				$this->assertSame( $v, $mock[ $k ] );
			}

			$this->assertEquals( $mock, $from_file );
		}

	}

	/**
	 * Test creating a new form
	 *
	 * @since 1.3.4
	 *
	 * @group db
	 * @group db_form_config
	 *
	 * @covers Caldera_Forms_Forms::create_form
	 */
	public function test_create_forms(){

		$data = array(
			"name" 			=> 'pants',
			"description" 	=> 'face',
		);

		$new_form = Caldera_Forms_Forms::create_form( $data );
		$this->assertInternalType( 'array', $new_form );
		$this->assertArrayHasKey( 'ID', $new_form );
		$id = $new_form[ 'ID' ];
		$saved = get_option( $id, 0 );
		$expected = array_merge( $data, array(
			"success"		=>	__( 'Form has been successfully submitted. Thank you.', 'caldera-forms' ),
			"form_ajax"		=> 1,
			"hide_form"		=> 1,
			"check_honey" 	=> 1,
			"db_support"    => 1,
			'mailer'		=>	array( 'on_insert' => 1 )
		));
		$expected[ 'ID' ] = $id;

		$this->assertArrayHasKey( $id, get_option( '_caldera_forms_forms', array() ) );
		$this->assertSame( $id, $saved[ "ID" ] );
		$this->assertEquals( $expected, $saved );
		$this->assertEquals( $expected, $new_form );

	}

	/**
	 * Test getting a form config when form is saved in DB
	 *
	 * @since 1.3.4
	 *
	 * @covers Caldera_Forms_Forms::get_form
	 */
	public function test_get_form_from_db() {
		$data = array(
			"name" 			=> 'pants',
			"description" 	=> 'face',
		);

		$new_form = Caldera_Forms_Forms::create_form( $data );
		$id = $new_form[ 'ID' ];
		$this->assertSame( $new_form, Caldera_Forms_Forms::get_form( $id ) );
		$this->assertSame( get_option( $id ), Caldera_Forms_Forms::get_form( $id ) );

	}

	/**
	 * Test updating a form config when form is saved in DB
	 *
	 * @since 1.3.4
	 *
	 * @group db
	 * @group db_form_config
	 *
	 * @covers Caldera_Forms_Forms::save_form
	 */
	public function test_update_form_from_db() {
		$data = array(
			"name" 			=> 'x',
			"description" 	=> 'y',
		);

		$new_form = Caldera_Forms_Forms::create_form( $data );
		$id = $new_form[ 'ID' ];
		$mock = $this->mock_form;
		$mock[ 'ID' ] = $id;
		$mock[ 'name' ] = 'x';
		$mock[ 'description' ] = 'y';
		$_id = Caldera_Forms_Forms::save_form( $mock );
		$this->assertSame( $id, $_id );
		$option = get_option( $id, array() );
		$this->assertFalse( empty( $option ) );
		$method = Caldera_Forms_Forms::get_form( $id );
		$this->assertFalse( empty( $method ) );

		//make sure saved form has these keys, then unset them since mock will not have them.
		$keys_that_will_screw_up_assertions = array( '_last_updated', 'ID', 'version' );
		foreach ( $keys_that_will_screw_up_assertions as $key ) {
			$this->assertArrayHasKey( $key, $method );
			$this->assertArrayHasKey( $key, $option );
			unset( $mock[ $key ] );
			unset( $option[ $key ] );
			unset( $method[ $key ] );
		}

		foreach ( $mock as $k => $v ) {
			$this->assertSame( $v, $option[ $k ] );
			$this->assertSame( $v, $method[ $k ] );
		}


		foreach ( $option as $k => $v ) {
			$this->assertSame( $v, $mock[ $k ] );
			$this->assertSame( $v, $method[ $k ] );
		}

		foreach ( $method as $k => $v ) {
			$this->assertSame( $v, $mock[ $k ] );
			$this->assertSame( $v, $option[ $k ] );
		}

		$this->assertSame( $option, $method );

	}

	/**
	 * Test registry stays in sync properly and does not cause cache errors
	 *
	 * @since 1.3.4
	 * 
	 * @group db
	 * @group db_form_config
	 *
	 * @covers Caldera_Forms_Forms::test_update_registry
	 */
	public function test_update_registry(){
		delete_option( '_caldera_forms_forms' );
		wp_cache_delete( '_caldera_forms_forms', 'options' );
		$ids = array();

		$this->assertSame( 0, count( get_option( '_caldera_forms_forms', array() ) ) );

		$data = array(
			"name"        => rand(),
			"description" => rand(),
		);

		$new_form = Caldera_Forms_Forms::create_form( $data );

		$id = $new_form[ 'ID' ];
		$this->assertArrayHasKey( $id, get_option( '_caldera_forms_forms', array() ) );
		$this->assertSame( Caldera_Forms_Forms::get_forms( false, true ), get_option( '_caldera_forms_forms' ) );
		$ids[] = $id;
		$data  = array(
			"name"        => rand(),
			"description" => rand(),
		);

		$new_form = Caldera_Forms_Forms::create_form( $data );


		$this->assertSame( Caldera_Forms_Forms::get_forms( false, true ), get_option( '_caldera_forms_forms' ) );


	}
	

}
