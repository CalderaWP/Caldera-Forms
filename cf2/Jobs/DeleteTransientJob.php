<?php


namespace calderawp\calderaforms\cf2\Jobs;


class DeleteTransientJob extends Job
{

	protected $transientId;
	public function __construct($transientId)
	{
		$this->transientId = $transientId;
	}


	public function handle()
	{
		\Caldera_Forms_Transient::delete_transient($this->transientId);
	}
}