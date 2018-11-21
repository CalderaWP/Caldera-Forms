<?php


namespace calderawp\calderaforms\cf2\Services;


use calderawp\calderaforms\cf2\CalderaFormsV2Contract;
use calderawp\calderaforms\cf2\Jobs\DatabaseConnection;
use WP_Queue\Queue;

class QueueService extends Service
{


	public function isSingleton()
	{
		return true;
	}

	public function register(CalderaFormsV2Contract $container)
	{
		return new Queue(new DatabaseConnection($container->getWpdb()));
	}

}