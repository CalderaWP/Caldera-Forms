<?php


namespace calderawp\calderaforms\cf2\Jobs;


class XJob
extends \calderawp\calderaforms\cf2\Jobs\Job {

	protected $x;
	public function __construct($x)
	{
		$this->x = $x;
	}

	public function handle()
	{
		wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_content' => 'fasfsd', 'post_title' => 'job1' . $this->x ] );
	}
}