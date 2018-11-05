<?php


namespace calderawp\calderaforms\Tests\Util\Mocks;


use calderawp\calderaforms\cf2\Jobs\Job;

class MockJob extends Job
{

	protected $callBackFunc;


	public function setCallback($callBackFunc){
		$this->callBackFunc = $callBackFunc;
	}
	public function handle()
	{
		call_user_func($this->callBackFunc);
	}
}