<?php


namespace calderawp\calderaforms\Tests\Util\Mocks;


use WP_Queue\Connections\ConnectionInterface;
use WP_Queue\Connections\SyncConnection;
use WP_Queue\Job;

class MockQueueConnection extends SyncConnection
{

	protected $jobs;

	public function push( Job $job, $delay = 0 ){
		$this->jobs[] = $job;
		return true;
	}


	public function getRemainingJobCount(){

		return is_array( $this->jobs) ? count( $this->jobs ) : 0;
	}
	public function pop()
	{
		if( ! is_array( $this->jobs ) ){
			return false;
		}
		return array_pop($this->jobs );
	}
}