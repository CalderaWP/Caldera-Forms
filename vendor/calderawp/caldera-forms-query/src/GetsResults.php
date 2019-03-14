<?php


namespace calderawp\CalderaFormsQuery;

interface GetsResults
{
	/**
	 * @param $sql
	 * @return \stdClass[]
	 */
	public function getResults($sql);
}
