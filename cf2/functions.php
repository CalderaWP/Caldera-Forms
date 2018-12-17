<?php

/**
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