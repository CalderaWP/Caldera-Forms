<?php

namespace calderawp\CalderaFormsQuery\Select;

/**
 * Interface DoesSelectQueryByEntryId
 *
 * Interface that any class that selects by entry ID MUST implement.
 */
interface DoesSelectQueryByEntryId
{


	/**
	 * Create query by entry ID
	 *
	 * @param $entryId
	 * @return $this
	 */
	public function queryByEntryId($entryId);

	/**
	 * Get name of ID column
	 *
	 * @return string
	 */
	public function getEntryIdColumn();
}
