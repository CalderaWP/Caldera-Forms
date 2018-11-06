<?php

namespace calderawp\calderaforms\Tests\Unit\Service;

use calderawp\calderaforms\cf2\Services\QueueService;
use calderawp\calderaforms\Tests\Integration\TestCase;
use WP_Queue\Queue;

class QueueServiceTest extends TestCase
{

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Services\QueueService::getIdentifier()
	 */
	public function testRegister()
	{
		$container = $this->getContainer();
		$service = new QueueService();

		$container->registerService($service,true );
		$this->assertInstanceOf( Queue::class, $container->getService(QueueService::class) );


	}

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Services\QueueService::isSingleton()
	 */
	public function testIsSingleton()
	{
		$service = new QueueService();
		$this->assertTrue( $service->isSingleton() );
	}

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Services\QueueService::getIdentifier()
	 */
	public function testGetIdentifier()
	{
		$service = new QueueService();
		$this->assertTrue( is_string($service->getIdentifier()) );
		$this->assertSame(QueueService::class, $service->getIdentifier() );
	}
}
