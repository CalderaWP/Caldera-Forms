<?php

namespace calderawp\calderaforms\Tests\Integration\Jobs;

use calderawp\calderaforms\cf2\Jobs\Scheduler;
use calderawp\calderaforms\Tests\Integration\TestCase;
use calderawp\calderaforms\Tests\Util\Mocks\MockJob;
use calderawp\calderaforms\Tests\Util\Mocks\MockQueueConnection;
use Mockery\Mock;
use WP_Queue\Queue;
use WP_Queue\Worker;

class SchedulerTest extends TestCase
{

	/**
	 * Tracks if callback function for mock job
	 *
	 * @since 1.8.0
	 *
	 * @type boolean
	 */
	protected $callbackRan;

	/** @inheritdoc */
	public function setUp()
	{
		$this->callbackRan = FALSE;

		parent::setUp();
	}


	/**
	 * Callback function for mock job
	 *
	 * @since 1.8.0
	 */
	public function callbackFunction()
	{
		$this->callbackRan = TRUE;
	}

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers MockQueueConnection::getRemainingJobCount()
	 */
	public function testMockConnection()
	{
		$connection = new MockQueueConnection();
		$connection->push(new MockJob());
		$this->assertSame(1, $connection->getRemainingJobCount());
	}

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers Scheduler::getWorker()
	 */
	public function testGetWorker()
	{
		$queue = new Queue(new MockQueueConnection());
		$scheduler = new Scheduler($queue);
		$this->assertInstanceOf(Worker::class, $scheduler->getWorker());


	}

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers Scheduler::runWithCron()
	 * @covers Scheduler::schedule()
	 */
	public function testSchedule()
	{
		$queue = new Queue(new MockQueueConnection());
		$scheduler = new Scheduler($queue);
		$job = new MockJob();
		$job->setCallback($this->callbackFunction());
		$scheduler->schedule($job);
		$scheduler->runWithCron();
		$this->assertTrue($this->callbackRan);
	}

	/**
	 *
	 * @since 1.8.0
	 *
	 * @covers Scheduler::schedule()
	 * @covers Scheduler::getWorker()
	 */
	public function testRunWithCron()
	{
		$queue = new Queue(new MockQueueConnection());
		$scheduler = new Scheduler($queue);
		$job = new MockJob();
		$job->setCallback($this->callbackFunction());
		$scheduler->schedule($job);
		$scheduler->runWithCron();
		$this->assertTrue($this->callbackRan);
	}


	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Jobs\Scheduler::runJobs();
	 */
	public function testWorkJobs()
	{
		$connection = new MockQueueConnection();
		$queue = new Queue($connection);
		$scheduler = new Scheduler($queue);
		$job = new MockJob();
		$job->setCallback($this->callbackFunction());
		$scheduler->schedule($job);
		$scheduler->schedule($job);
		$scheduler->schedule($job);
		$numberOfJobs = 3;
		$this->assertEquals($numberOfJobs, $scheduler->runJobs($numberOfJobs));
		$this->assertEquals(0, $connection->getRemainingJobCount());
	}


	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Jobs\Scheduler::runJobs();
	 */
	public function testWorksRightNumberOfJobs()
	{
		$connection = new MockQueueConnection();
		$queue = new Queue($connection);
		$scheduler = new Scheduler($queue);
		$job = new MockJob();
		$job->setCallback($this->callbackFunction());
		$scheduler->schedule($job);
		$scheduler->schedule($job);
		$scheduler->schedule($job);
		$numberOfJobs = 2;
		$this->assertEquals($numberOfJobs, $scheduler->runJobs($numberOfJobs));
		$this->assertEquals(1, $connection->getRemainingJobCount());
	}


	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Jobs\Scheduler::runJobs();
	 */
	public function testWorksJobsUntilOutOfPossibleJobs()
	{
		$connection = new MockQueueConnection();
		$queue = new Queue($connection);
		$scheduler = new Scheduler($queue);
		$job = new MockJob();
		$job->setCallback($this->callbackFunction());
		$scheduler->schedule($job);
		$scheduler->schedule($job);
		$scheduler->schedule($job);
		$numberOfJobs = 5;
		$this->assertEquals(3, $scheduler->runJobs($numberOfJobs));
		$this->assertEquals(0, $connection->getRemainingJobCount());
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Jobs\Scheduler::runJobs();
	 */
	public function testWorksLeftOverJobs()
	{
		$connection = new MockQueueConnection();
		$queue = new Queue($connection);
		$scheduler = new Scheduler($queue);
		$job = new MockJob();
		$job->setCallback($this->callbackFunction());
		$scheduler->schedule($job);
		$scheduler->schedule($job);
		$scheduler->schedule($job);
		$numberOfJobs = 2;
		$this->assertEquals($numberOfJobs, $scheduler->runJobs($numberOfJobs));
		$this->assertEquals(1, $connection->getRemainingJobCount());

		$this->assertEquals(1, $scheduler->runJobs($numberOfJobs));
		$this->assertEquals(0, $connection->getRemainingJobCount());

	}

}
