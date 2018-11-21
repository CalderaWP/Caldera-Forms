<?php

namespace calderawp\calderaforms\Tests\Integration\Service;

use calderawp\calderaforms\cf2\Jobs\Scheduler;
use calderawp\calderaforms\cf2\Services\QueueSchedulerService;
use calderawp\calderaforms\cf2\Services\QueueService;
use calderawp\calderaforms\Tests\Integration\TestCase;

class QueueSchedulerServiceTest extends TestCase
{

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Services\QueueSchedulerService::getIdentifier()
	 */
	public function testGetIdentifier()
	{
		$service = new QueueSchedulerService();
		$this->assertSame(QueueSchedulerService::class, $service->getIdentifier());
	}

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Services\QueueSchedulerService::isSingleton()
	 */
	public function testIsSingleton()
	{
		$service = new QueueSchedulerService();
		$this->assertTrue($service->isSingleton());
	}


	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Services\QueueSchedulerService::register()
	 */
	public function testRegister()
	{
		$service = new QueueSchedulerService();
		$container = $this->getContainer();

		$this->assertInstanceOf(Scheduler::class, $service->register($container));

	}

}
