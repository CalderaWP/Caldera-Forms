<?php

/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class Test_Caldera_Forms_Entry_Objects extends Caldera_Forms_Test_Case{

	/**
	 * Test that entry object gets and sets properly
	 *
	 * @since 1.4.0
	 *
	 * @group db
	 * @group entry
	 * @group entry_objects
	 *
	 * @covers Caldera_Forms_Entry_Object::__construct()
	 * @covers Caldera_Forms_Entry_Object::set_form_object()
	 * @covers Caldera_Forms_Entry_Object::__set()
	 * @covers Caldera_Forms_Entry_Object::__get()
	 * @covers Caldera_Forms_Entry_Object::to_array()
	 */
	public function testEntry(){
		$now = current_time( 'mysql' );
		$data = array(
			'id' => 4,
			'form_id' => 'cf12345',
			'user_id' => 1,
			'datestamp' => $now,
			'status' => 'active',
		);

		$object = new Caldera_Forms_Entry_Entry;
		foreach( $data as $key => $value ){
			$object->$key = $value;
			$this->assertEquals( $value, $object->$key );
		}

		$this->assertEquals( $data, $object->to_array() );

		$object = new Caldera_Forms_Entry_Entry( (object) $data );
		foreach( $data as $key => $value ){
			$this->assertEquals( $value, $object->$key );
		}

		$this->assertEquals( $data, $object->to_array() );
	}

	/**
	 * Test that field object gets and sets properly
	 *
	 * @since 1.4.0
	 *
	 * @group db
	 * @group entry
	 * @group entry_objects
	 *
	 * @covers Caldera_Forms_Entry_Object::__construct()
	 * @covers Caldera_Forms_Entry_Object::set_form_object()
	 * @covers Caldera_Forms_Entry_Object::__set()
	 * @covers Caldera_Forms_Entry_Object::__get()
	 * @covers Caldera_Forms_Entry_Object::to_array()
	 */
	public function testField(){

		$data = array(
			'id' => rand(),
			'field_id' => 'fld000',
			'entry_id' => 5,
			'slug' => 'batman',
			'value' => 'robin'
		);
		$object = new Caldera_Forms_Entry_Field;
		foreach( $data as $key => $value ){
			$object->$key = $value;

			$this->assertEquals( $value, $object->$key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$object = new Caldera_Forms_Entry_Field( (object) $data );
		foreach( $data as $key => $value ){
			$this->assertEquals( $value, $object->$key, $key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$data[ 'value' ] = array( 'tacos', 'burritos' );
		$object = new Caldera_Forms_Entry_Field;
		foreach( $data as $key => $value ){
			$object->$key = $value;

			$this->assertEquals( $value, $object->$key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$object = new Caldera_Forms_Entry_Field( (object) $data );
		foreach( $data as $key => $value ){
			$this->assertEquals( $value, $object->$key, $key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$data[ 'value' ] = array( 'tacos', 'burritos', 1 => 'hats' );
		$object = new Caldera_Forms_Entry_Field;
		foreach( $data as $key => $value ){
			$object->$key = $value;

			$this->assertEquals( $value, $object->$key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$object = new Caldera_Forms_Entry_Field( (object) $data );
		foreach( $data as $key => $value ){
			$this->assertEquals( $value, $object->$key, $key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$data[ 'value' ] = json_encode(  array( 'tacos', 'burritos', 1 => 'hats' ) );
		$object = new Caldera_Forms_Entry_Field;
		foreach( $data as $key => $value ){
			$object->$key = $value;

			$this->assertEquals( $value, $object->$key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$object = new Caldera_Forms_Entry_Field( (object) $data );
		foreach( $data as $key => $value ){
			$this->assertEquals( $value, $object->$key, $key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );


	}

	/**
	 * Test that meta object gets and sets properly
	 *
	 * @since 1.4.0
	 *
	 * @group db
	 * @group entry
	 * @group entry_objects
	 *
	 * @covers Caldera_Forms_Entry_Object::__construct()
	 * @covers Caldera_Forms_Entry_Object::set_form_object()
	 * @covers Caldera_Forms_Entry_Object::__set()
	 * @covers Caldera_Forms_Entry_Object::__get()
	 * @covers Caldera_Forms_Entry_Object::to_array()
	 */
	public function testMeta(){

		$data = array(
			'meta_id' => 5,
			'entry_id' => rand(),
			'process_id' => 'fp_' . rand(),
			'meta_key' => 'spidermen' ,
			'meta_value' => 'three to six spidermen'
		);

		$object = new Caldera_Forms_Entry_Meta();
		foreach( $data as $key => $value ){
			$object->$key = $value;

			$this->assertEquals( $value, $object->$key, $key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$object = new Caldera_Forms_Entry_Meta( (object) $data );
		foreach( $data as $key => $value ){
			$this->assertEquals( $value, $object->$key, $key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$data[ 'meta_value' ] = array( 'tacos', 'burritos' );
		$object = new Caldera_Forms_Entry_Meta;
		foreach( $data as $key => $value ){
			$object->$key = $value;

			$this->assertEquals( $value, $object->$key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$object = new Caldera_Forms_Entry_Meta( (object) $data );
		foreach( $data as $key => $value ){
			$this->assertEquals( $value, $object->$key, $key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$data[ 'meta_value' ] = array( 'tacos', 'burritos', 1 => 'hats' );
		$object = new Caldera_Forms_Entry_Meta;
		foreach( $data as $key => $value ){
			$object->$key = $value;

			$this->assertEquals( $value, $object->$key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$object = new Caldera_Forms_Entry_Meta( (object) $data );
		foreach( $data as $key => $value ){
			$this->assertEquals( $value, $object->$key, $key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$data[ 'meta_value' ] = json_encode(  array( 'tacos', 'burritos', 1 => 'hats' ) );
		$object = new Caldera_Forms_Entry_Meta;
		foreach( $data as $key => $value ){
			$object->$key = $value;
			$this->assertEquals( $value, $object->$key, $key );

		}

		$this->assertEquals( $data, $object->to_array( false ) );

		$object = new Caldera_Forms_Entry_Meta( (object) $data );
		foreach( $data as $key => $value ){
			$this->assertEquals( $value, $object->$key, $key );
		}

		$this->assertEquals( $data, $object->to_array( false ) );


	}

	/**
	 * Test that we CAN NOT set invalid data
	 *
	 * @since 1.4.0
	 *
	 * @group db
	 * @group entry
	 * @group entry_objects
	 *
	 * @covers Caldera_Forms_Entry_Object::__set()
	 */
	public function testSetInvalid(){
		$obj = new Caldera_Forms_Entry_Entry();
		$obj->batman = 'spiderman';
		$this->assertObjectNotHasAttribute( 'batman', $obj  );

		$obj = new Caldera_Forms_Entry_Field();
		$obj->hulk = 'spiderman';
		$this->assertObjectNotHasAttribute( 'hulk', $obj  );

		$obj = new Caldera_Forms_Entry_Meta();
		$obj->thor = 'spiderman';
		$this->assertObjectNotHasAttribute( 'thor', $obj  );
		
	}

}
