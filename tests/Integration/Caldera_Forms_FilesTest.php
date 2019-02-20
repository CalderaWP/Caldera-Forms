<?php

namespace calderawp\calderaforms\Tests\Integration;

use Caldera_Forms_Files;
use calderawp\calderaforms\Tests\Util\Traits\TestsImages;

class Caldera_Forms_FilesTest extends TestCase
{

	use TestsImages;
	protected $field = [
		'ID' => 'fld123',
		'type' => 'file',
		'config' => [

		],
	];

	/** @inheritdoc */
	public function tearDown()
	{
		$this->deleteTestCatFile();
		parent::tearDown();
	}

	/**
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

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers Caldera_Forms_Files::get_max_upload_size()
	 */
	public function testGetMaxUploadSize()
	{
		$field = array_merge($this->field, [
			'config' => [ 'max_upload' => 42 ],
		]);
		//returns saved value
		$this->assertEquals( 42, Caldera_Forms_Files::get_max_upload_size($field ) );

		$field = array_merge($this->field, [
			'config' => [ 'max_upload' => '42' ],
		]);
		//always a string
		$this->assertEquals( 42, Caldera_Forms_Files::get_max_upload_size($field ) );

		//0 by default
		$this->assertEquals( 0, Caldera_Forms_Files::get_max_upload_size($this->field ) );
	}

	/**
	 **
	 * @since 1.8.0
	 *
	 * @covers Caldera_Forms_Files::is_file_too_large()
	 * @covers Caldera_Forms_Files::get_max_upload_size()
	 */
	public function testIfFileIsTooLarge(){
		$field = array_merge($this->field, [
			'config' => [ 'max_upload' => 42 ],
		]);

		//This file is larger than 42 bytes
		$this->assertTrue(
			Caldera_Forms_Files::is_file_too_large($field, $this->createSmallCat() )
		);


		$field = array_merge($this->field, [
			'config' => [ 'max_upload' => 42000000 ],
		]);

		//This file is smaller than 42000000 bytes
		$this->assertFalse(
			Caldera_Forms_Files::is_file_too_large($field, $this->createSmallCat() )
		);


		//No limits, all files are the right size.
		$this->assertFalse(
			Caldera_Forms_Files::is_file_too_large($this->field, $this->createSmallCat() )
		);
	}

}
