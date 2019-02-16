<?php

use PHPUnit\Framework\TestCase;
use WP_Queue\Connections\ConnectionInterface;
use WP_Queue\Job;
use WP_Queue\Worker;

class TestWorker extends TestCase {

	public function setUp() {
		WP_Mock::setUp();
	}

	public function tearDown() {
		WP_Mock::tearDown();
	}

	public function test_process_success() {
		$connection = Mockery::spy( ConnectionInterface::class );
		$job        = Mockery::spy( Job::class );
		$connection->shouldReceive( 'pop' )->once()->andReturn( $job );

		$worker = new Worker( $connection );
		$this->assertTrue( $worker->process() );
	}

	public function test_process_fail() {
		$connection = Mockery::spy( ConnectionInterface::class );
		$job        = Mockery::spy( Job::class );
		$connection->shouldReceive( 'pop' )->once()->andReturn( false );

		$worker = new Worker( $connection );
		$this->assertFalse( $worker->process() );
	}
}