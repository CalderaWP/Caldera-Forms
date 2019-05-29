<?php


namespace calderawp\CalderaFormsQuery\Delete;

use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;

/**
 * Interface DoesDeleteQuery
 *
 * Interface that delete query builders MUST implement.
 */
interface DoesDeleteQuery
{

	/**
	 * Get current delete query
	 *
	 * @return Delete
	 */
	public function getCurrentQuery();
}
