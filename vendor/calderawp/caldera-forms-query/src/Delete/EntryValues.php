<?php


namespace calderawp\CalderaFormsQuery\Delete;

class EntryValues extends DeleteQueryBuilder
{

	/**
	 * Delete field by entry ID
	 *
	 * @param int $entryId Entry ID
	 * @return $this
	 */
	public function deleteByEntryId($entryId)
	{
		$this
			->getDeleteQuery()
			->where()
			->equals('entry_id', (int)$entryId)
		;
		return $this;
	}

	/**
	 * Delete a collection of entry values that are for a a set of entries.
	 *
	 * @param array $entryIds
	 * @return $this
	 */
	public function deleteByEntryIds(array $entryIds)
	{
		return $this->in($entryIds, 'entry_id');
	}

	/**
	 * Delete all field values with a value
	 *
	 * @param string $fieldSlug
	 * @param string $fieldValue
	 * @return $this
	 */
	public function deleteByFieldValue($fieldSlug, $fieldValue)
	{
		$this
			->getDeleteQuery()
			->where()
			->equals('value', $fieldValue)

		;

		$this
			->getDeleteQuery()
			->where()
			->equals('slug', $fieldSlug);
		return $this;
	}
}
