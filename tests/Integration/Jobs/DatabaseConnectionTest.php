<?php

namespace calderawp\calderaforms\Tests\Integration\Jobs;


use calderawp\calderaforms\cf2\Jobs\DatabaseConnection;
use calderawp\calderaforms\cf2\Jobs\Scheduler;
use calderawp\calderaforms\Tests\Integration\TestCase;
use calderawp\calderaforms\Tests\Util\Mocks\MockJob;
use WP_Queue\Queue;

class DatabaseConnectionTest extends TestCase
{




	/**
	 * @since 1.8.0
	 *
	 * @covers  \calderawp\calderaforms\cf2\Jobs\Scheduler::schedule()
	 */
	public function testSchedule()
	{
		global $wpdb;
		$connection = new DatabaseConnection($wpdb);
		$queue = new Queue($connection);
		$scheduler = new Scheduler($queue);
		$scheduler->schedule(new MockJob('foo') );
		$r = $wpdb->get_results( sprintf('SELECT * FROM %s;', $wpdb->prefix. DatabaseConnection::QUEUED_JOBS_TABLE) );
		$this->assertNotEmpty( $r[0]);
		$this->assertEquals( 0,$r[0]->attempts);

		$this->assertSame(1,$this->getJobCount($wpdb));
	}

	/**
	 * @since 1.8.0
	 *
	 * @param \wpdb $wpdb
	 *
	 * @return int
	 */
	protected function getJobCount(\wpdb $wpdb){

		$r =   $wpdb->get_results( sprintf('SELECT COUNT(*) FROM %s;', $wpdb->prefix. DatabaseConnection::QUEUED_JOBS_TABLE),ARRAY_A );
		return (int) $r[0]['COUNT(*)'];
	}
}
