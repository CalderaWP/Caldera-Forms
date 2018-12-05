<?php


namespace calderawp\calderaforms\cf2\Jobs;


use WP_Queue\Queue;

class Scheduler
{
	/**
	 * Queue manager
	 *
	 * @since 1.8.0
	 *
	 * @var Queue
	 */
	protected $queue;

	/**
	 * Scheduler constructor.
	 *
	 * @since  1.8.0
	 *
	 * @param Queue $queue
	 */
	public function __construct(Queue $queue)
	{
		$this->queue = $queue;
	}

	/**
	 * Get the queue worker
	 *
	 * @since  1.8.0
	 *
	 * @return \WP_Queue\Worker
	 */
	public function getWorker(){
		return $this->queue->worker(2);
	}

	/**
	 * Schedule a job to run
	 *
	 * @since 1.8.0
	 *
	 * @param Job $job Job to schedule.
	 * @param int $delay Optional. Delay in seconds until job runs. Default is 0.
	 */
	public function schedule(Job $job, $delay = 0){
		$this->queue->push($job, $delay );
	}

	/**
	 * Run the queue with WP_Cron
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	public function runWithCron()
	{
		return $this->queue->cron(2)->init();
	}

	/**
	 * Run jobs independently of WP_Cron
	 *
	 * @since 1.8.0
	 *
	 * @param int $numberOfJobs
	 *
	 * @return int
	 */
	public function runJobs($numberOfJobs = 5){
		$worker = $this->getWorker();
		$jobsRan = 0;
		for( $totalJobs = 0; $totalJobs < $numberOfJobs; $totalJobs++ ){
			if (!$worker->process()) {
				return $jobsRan;
			}
			$jobsRan++;

		}

		return $jobsRan;

	}

}