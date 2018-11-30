<?php

namespace calderawp\calderaforms\Tests\Unit\Fields\Handlers\FileUpload;

use calderawp\calderaforms\cf2\Fields\Handlers\FileUpload;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Unit\TestCase;
use calderawp\calderaforms\Tests\Util\Mocks\MockUploader;

class FileUploadTest extends TestCase
{

	/**
	 * Test setup of properties
	 *
	 * @since 1.8.0
	 *
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::__construct()
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$field
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$form
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::$uploader
	 */
	public function test__construct()
	{
		$field = [ 'ID' => 'fld1' ];
		$form = [ 'ID' => 'cd1' ];
		$uploader = new MockUploader();
		$handler = new FileUpload($field, $form, $uploader);
		$this->assertAttributeEquals($field, 'field', $handler);
		$this->assertAttributeEquals($form, 'form', $handler);
		$this->assertAttributeEquals($uploader, 'uploader', $handler);
	}

	/**
	* @since 1.8.0
	*
	* @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::getAllowedTypes()
	*/
	public function testGetAllowedTypesWithOneAllowedType()
	{
		$field = [ 'ID' => 'fld1', 'config' => [ 'allowed' => 'png' ] ];
		$form = [ 'ID' => 'cd1' ];
		$uploader = new MockUploader();
		$handler = new FileUpload($field, $form, $uploader);
		$this->assertTrue(in_array('png', $handler->getAllowedTypes()));

	}

	/**
	 * @since 1.8.0
	 *
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::getAllowedTypes()
	 */
	public function testGetAllowedTypesWithOneAllowedTypes()
	{
		$field = [ 'ID' => 'fld1', 'config' => [ 'allowed' => 'png,pdf' ] ];
		$form = [ 'ID' => 'cd1' ];
		$uploader = new MockUploader();
		$handler = new FileUpload($field, $form, $uploader);
		$this->assertTrue(in_array('png', $handler->getAllowedTypes()));
		$this->assertTrue(in_array('pdf', $handler->getAllowedTypes()));

	}

	/**
	 * Test that jpeg is allowed type, if jpg is.
	 *
	 * @since 1.8.0
	 *
	 * @cover \calderawp\calderaforms\cf2\Fields\Handlers\FileUpload::getAllowedTypes()
	 */
	public function testGetAllowedTypesIncludesJpegIfJpgAllowed()
	{
		$field = [ 'ID' => 'fld1', 'config' => [ 'allowed' => 'png,jpg' ] ];
		$form = [ 'ID' => 'cd1' ];
		$uploader = new MockUploader();
		$handler = new FileUpload($field, $form, $uploader);
		$this->assertTrue(in_array('jpg', $handler->getAllowedTypes()));
		$this->assertTrue(in_array('jpeg', $handler->getAllowedTypes()));

	}
}
