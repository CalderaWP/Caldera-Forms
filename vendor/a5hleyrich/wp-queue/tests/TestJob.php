<?php

use PHPUnit\Framework\TestCase;
use WP_Queue\Job;

class TestJobAbstract extends TestCase {

	protected $instance;

	public function setUp() {
		WP_Mock::setUp();
		$this->instance = $this->getMockForAbstractClass( Job::class );
	}

	public function tearDown() {
		WP_Mock::tearDown();
	}

	public function test_release() {
		$this->assertFalse( $this->instance->released() );
		$this->instance->release();
		$this->assertTrue( $this->instance->released() );
	}

	public function test_fail() {
		$this->assertFalse( $this->instance->failed() );
		$this->instance->fail();
		$this->assertTrue( $this->instance->failed() );
	}
}