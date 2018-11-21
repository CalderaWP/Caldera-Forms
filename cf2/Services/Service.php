<?php


namespace calderawp\calderaforms\cf2\Services;


abstract class Service implements ServiceContract
{



	final public function getIdentifier()
	{
		return static::class;
	}
}