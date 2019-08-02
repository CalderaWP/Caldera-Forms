<?php

namespace calderawp\calderaforms\Tests\Integration\Handlers;

use calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader;
use calderawp\calderaforms\Tests\Integration\TestCase;

class Cf1FileUploaderTest extends TestCase
{
	protected $test_file;


	public function setUp()
	{
		$orig_file = __DIR__ . '/screenshot.jpeg';
		$this->test_file = '/tmp/screenshot.jpg';
		copy($orig_file, $this->test_file);
		parent::setUp();
	}


	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader::upload()
	 *
	 * @group file
	 */
	public function testUpload()
	{
		$file = [
			'file' => file_get_contents($this->test_file),
			'name' => 'screenshot.jpeg',
			'size' => filesize($this->test_file),
			'tmp_name' => $this->test_file,
		];
		$uploadArgs = [
			'private' => true,
			'field_id' => 'fld1',
			'form_id' => 'cf1'
		];

		$uploader = new Cf1FileUploader();
		$uploads = $uploader->upload($file, $uploadArgs);
		$this->assertTrue( is_array( $uploads ) );
		$this->assertFalse( is_wp_error( $uploads ) );

	}
	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader::addFilter()
	 *
	 * @group file
	 */
	public function testAddFilter()
	{
		$uploader = new Cf1FileUploader();
		$uploader->addFilter('f', 'c', true );
		$this->assertSame(10, has_filter( 'upload_dir',  [\Caldera_Forms_Files::class, 'uploads_filter' ] ) );
	}
	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader::addFilter()
	 * @covers \calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader::removeFilter()
	 *
	 * @group file
	 */
	public function testRemoveFilter()
	{
		$uploader = new Cf1FileUploader();
		$uploader->addFilter('f', 'c', true );
		$uploader->removeFilter();
		$this->assertFalse( has_filter( 'upload_dir',  [\Caldera_Forms_Files::class, 'uploads_filter' ] ) );
	}


}
