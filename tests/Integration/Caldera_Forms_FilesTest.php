<?php

namespace calderawp\calderaforms\Tests\Integration;

use Caldera_Forms_Files;

class Caldera_Forms_FilesTest extends TestCase
{

	protected $field = [
		'ID' => 'fld123',
		'type' => 'file',
		'config' => [

		],
	];

	/**
	 * @group now
	 *
	 * @since  1.8.0
	 *
	 * @covers Caldera_Forms_Files::should_attach()
	 */
	public function testShould_attach()
	{
		$field = array_merge($this->field, [
			'config' => [ 'attach' => true ],
		]);

		//Is set to attatch
		$this->assertTrue(
			Caldera_Forms_Files::should_attach($field, [])
		);

		//No value, default false
		$this->assertFalse(
			Caldera_Forms_Files::should_attach($this->field, [])
		);

		$field = array_merge($this->field, [
			'config' => [ 'attach' => false ],
		]);
	//False value
		$this->assertFalse(
			Caldera_Forms_Files::should_attach($field, [])
		);

	}

	/**
	 * @group now
	 *
	 * @since  1.8.0
	 *
	 * @covers Caldera_Forms_Files::is_private()
	 */
	public function testIs_private()
	{
		$field = array_merge($this->field, [
			'config' => [ 'media_lib' => false ],
		]);

		//Not in media library, is private
		$this->assertTrue(
			Caldera_Forms_Files::is_private($field)
		);

		//Not in media library by default, so is private
		$this->assertTrue(
			Caldera_Forms_Files::is_private($this->field)
		);

		$field = array_merge($this->field, [
			'config' => [ 'media_lib' => true ],
		]);

		//In media library, so is not private
		$this->assertFalse(
			Caldera_Forms_Files::is_private($field)
		);
	}

	/**
	 * @group now
	 *
	 * @since  1.8.0
	 *
	 * @covers Caldera_Forms_Files::is_persistent()
	 */
	public function testIs_persistent()
	{
		$field = array_merge($this->field, [
			'config' => [ 'media_lib' => true ],
		]);

		//In media library, so is peristant
		$this->assertTrue(
			Caldera_Forms_Files::is_persistent($field)
		);


		$field = array_merge($this->field, [
			'config' => [ 'media_lib' => false ],
		]);

		$this->assertFalse(
			Caldera_Forms_Files::is_persistent($field)
		);


		$field = array_merge($this->field, [
			'config' => [ 'persistent' => true ],
		]);

		//Is peristant
		$this->assertTrue(
			Caldera_Forms_Files::is_persistent($field)
		);

		$field = array_merge($this->field, [
			'config' => [ 'persistent' => false ],
		]);

		//Is not peristant
		$this->assertFalse(
			Caldera_Forms_Files::is_persistent($field)
		);

		//Is not peristant by default
		$this->assertFalse(
			Caldera_Forms_Files::is_persistent($this->field)
		);


	}

	/**
	 * @group now
	 *
	 * @since  1.8.0
	 *
	 * @covers Caldera_Forms_Files::should_add_to_media_library()
	 */
	public function testShould_add_to_media_library()
	{
		$field = array_merge($this->field, [
			'config' => [ 'media_lib' => true ],
		]);

		//add when true
		$this->assertTrue(
			Caldera_Forms_Files::should_add_to_media_library($field)
		);
		//not by default
		$this->assertFalse(
			Caldera_Forms_Files::should_add_to_media_library($this->field)
		);

		$field = array_merge($this->field, [
			'config' => [ 'media_lib' => false ],
		]);

		//not when false
		$this->assertFalse(
			Caldera_Forms_Files::should_add_to_media_library($field)
		);
	}
}
