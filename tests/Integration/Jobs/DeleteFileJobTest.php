<?php

namespace calderawp\calderaforms\Tests\Integration\Jobs;
use calderawp\calderaforms\Tests\Util\Traits\TestsImages;

use calderawp\calderaforms\cf2\Jobs\DeleteFileJob;

class DeleteFileJobTest extends \PHPUnit_Framework_TestCase
{
	use TestsImages;
	public function tearDown()
	{
		$this->deleteTestCatFile();
		parent::tearDown();
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Jobs\DeleteFileJob::__construct()
	 * @covers \calderawp\calderaforms\cf2\Jobs\DeleteFileJob::$path
	 */
	public function test__construct()
	{
		$job = new DeleteFileJob('foo/bar' );
		$this->assertAttributeEquals('foo/bar', 'path', $job );
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Jobs\DeleteFileJob::handle()
	 */
	public function testHandle()
	{
		//create file
		$file = $this->createSmallCat();
		//make sure file exists
		$this->assertTrue(file_exists($file['tmp_name']));
		//delete file
		$job = new DeleteFileJob($file['tmp_name'] );
		$job->handle();
		//make sure it is deleted
		$this->assertFalse(file_exists($file['tmp_name']));
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Jobs\DeleteFileJob::handle()
	 */
	public function testHandleWithNonExistantFile()
	{

		$job = new DeleteFileJob('noms/foods' );
		$job->handle(); // an exception being thrown here would be bad.
		$this->assertTrue(true);//@see https://github.com/sebastianbergmann/phpunit/issues/2484
	}
}
