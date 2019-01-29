<?php


namespace calderawp\calderaforms\Tests\Util\Mocks;


use calderawp\calderaforms\cf2\CalderaFormsV2Contract;
use calderawp\calderaforms\cf2\Services\Service;

class MockService extends Service
{


	public function register(CalderaFormsV2Contract $container)
	{
		$obj = new \stdClass();
		$obj->roy = 'Sivan';
		return $obj;
	}

	public function isSingleton()
	{
		return true;
	}
}