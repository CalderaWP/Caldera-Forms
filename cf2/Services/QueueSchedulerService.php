<?php


namespace calderawp\calderaforms\cf2\Services;


use calderawp\calderaforms\cf2\CalderaFormsV2Contract;
use calderawp\calderaforms\cf2\Jobs\DatabaseConnection;
use calderawp\calderaforms\cf2\Jobs\Scheduler;

use WP_Queue\Queue;
use WP_Queue\QueueManager;

class QueueSchedulerService extends Service
{

	/** @inheritdoc */
	public function isSingleton()
	{
		return true;
	}

	/** @inheritdoc */
	public function register(CalderaFormsV2Contract $container)
	{
		return new Scheduler( new Queue(new DatabaseConnection($container->getWpdb())) );
	}


}