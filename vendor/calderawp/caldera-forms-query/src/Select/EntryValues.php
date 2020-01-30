<?php


namespace calderawp\CalderaFormsQuery\Select;

/**
 * Class EntryValues
 *
 * Performs select queries for entry values
 */
class EntryValues extends ValueSelectQueryBuilder
{

	/** @inheritdoc */
	public function getValueColumn()
	{
		return 'value';
	}

	/** @inheritdoc */
	public function getEntryIdColumn()
	{
		return 'entry_id';
	}
}
