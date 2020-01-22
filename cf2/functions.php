<?php

/**
 * Get the cf2 container
 *
 * @since 1.8.0
 *
 * @return \calderawp\calderaforms\cf2\CalderaFormsV2Contract
 */
function caldera_forms_get_v2_container()
{

	static $container;
	if ( !$container ) {
		$container = new \calderawp\calderaforms\cf2\CalderaFormsV2();
		do_action('caldera_forms_v2_init', $container);
	}

	return $container;
}

/**
 * Setup Cf2 container
 *
 * @since 1.8.0
 *
 * @uses "caldera_forms_v2_init" action
 *
 * @param \calderawp\calderaforms\cf2\CalderaFormsV2Contract $container
 */
function caldera_forms_v2_container_setup(\calderawp\calderaforms\cf2\CalderaFormsV2Contract $container)
{
	$container
		//Set paths
		->setCoreDir(CFCORE_PATH)
		->setCoreUrl(CFCORE_URL)
		//Setup field types
		->getFieldTypeFactory()
		->add(new \calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType());

	//Add hooks
	$container->getHooks()->subscribe();

	//Register other services
	$container
		->registerService(new \calderawp\calderaforms\cf2\Services\QueueService(), true)
		->registerService(new \calderawp\calderaforms\cf2\Services\QueueSchedulerService(), true)
        ->registerService(new \calderawp\calderaforms\cf2\Services\FormsService(), true )
        ->registerService(new \calderawp\calderaforms\cf2\Services\ProcessorService(), true );


	//Run the scheduler with CRON
	/** @var \calderawp\calderaforms\cf2\Jobs\Scheduler $scheduler */
	$scheduler = $container->getService(\calderawp\calderaforms\cf2\Services\QueueSchedulerService::class);
	$running = $scheduler->runWithCron();
}

/**
 * Schedule delete with job manager
 *
 * @since 1.8.0
 *
 * @param \calderawp\calderaforms\cf2\Jobs\Job $job Job to schedule
 * @param int $delay Optional. Minimum delay before job is run. Default is 0.
 */
function caldera_forms_schedule_job(\calderawp\calderaforms\cf2\Jobs\Job $job, $delay = 0)
{

	caldera_forms_get_v2_container()
		->getService(\calderawp\calderaforms\cf2\Services\QueueSchedulerService::class)
		->schedule($job, $delay);
}
