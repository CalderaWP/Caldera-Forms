<?php


namespace calderawp\CalderaFormsQuery\Select;

use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

interface DoesSelectQuery
{

	/**
	 * Get Select query builder
	 *
	 * @return Select
	 */
	public function getSelectQuery();
}
