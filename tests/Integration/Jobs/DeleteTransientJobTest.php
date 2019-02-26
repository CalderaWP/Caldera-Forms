<?php

namespace calderawp\calderaforms\Tests\Integration\Jobs;

use calderawp\calderaforms\cf2\Jobs\DeleteTransientJob;
use calderawp\calderaforms\Tests\Integration\TestCase;

class DeleteTransientJobTest extends TestCase
{


	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Jobs\DeleteTransientJob::handle()
	 */
	public function testHandle()
	{
		$id = uniqid(rand());
		\Caldera_Forms_Transient::set_transient($id, 'foo', 9000 );
		$job = new DeleteTransientJob($id);
		$job->handle();
		$this->assertFalse(\Caldera_Forms_Transient::get_transient($id));
	}
}
