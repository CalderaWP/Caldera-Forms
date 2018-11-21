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
		$file = $this->createSmallCat();
		$job = new DeleteFileJob($file['tmp_name'] );
		$job->handle();
		$this->assertFalse(file_exists($file['tmp_name']));
	}
}
