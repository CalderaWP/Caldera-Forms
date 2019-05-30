<?php

use PHPUnit\Framework\TestCase;
use WP_Queue\Connections\ConnectionInterface;
use WP_Queue\Cron;
use WP_Queue\Job;
use WP_Queue\Queue;
use WP_Queue\Worker;

class TestQueue extends TestCase {

	public function setUp() {
		WP_Mock::setUp();
	}

	public function tearDown() {
		WP_Mock::tearDown();
	}

	public function test_push_success() {
		$insert_id  = 12345;
		$connection = Mockery::mock( ConnectionInterface::class );
		$connection->shouldReceive( 'push' )->once()->andReturn( $insert_id );

		$queue = new Queue( $connection );

		$this->assertEquals( $insert_id, $queue->push( new TestJob() ) );
	}

	public function test_push_fail() {
		$connection = Mockery::mock( ConnectionInterface::class );
		$connection->shouldReceive( 'push' )->once()->andReturn( false );

		$queue = new Queue( $connection );

		$this->assertFalse( $queue->push( new TestJob() ) );
	}

	public function test_cron() {
		$connection = Mockery::mock( ConnectionInterface::class );
		$queue      = new Queue( $connection );

		WP_Mock::userFunction( 'wp_next_scheduled', array(
			'return' => time(),
		) );

		$this->assertInstanceOf( Cron::class, $queue->cron() );
	}

	public function test_worker() {
		$connection = Mockery::mock( ConnectionInterface::class );
		$queue      = new Queue( $connection );

		$this->assertInstanceOf( Worker::class, $queue->worker( 3 ) );
	}
}

if ( ! class_exists( 'TestJob' ) ) {
	class TestJob extends Job {
		public function handle() {}
	}
}