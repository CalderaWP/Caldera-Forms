<?php


namespace calderawp\CalderaFormsQuery;

trait CanCollectResults
{
	/**
	 * Collect results using  Caldera_Forms_Entry_Entry and Caldera_Forms_Entry_Field to represent values
	 *
	 * @param \stdClass[] $entriesValues
	 * @return array
	 */
	private function collectResults($entriesValues)
	{
		$results = [];
		foreach ($entriesValues as $entry) {
			$this->resetEntryValueGenerator();
			$entry = new \Caldera_Forms_Entry_Entry($entry);
			$this
				->getEntryValueGenerator()
				->queryByEntryId($entry->id);
			$entriesValues =$this->getResults(
				$this->getEntryValueGenerator()
					->getPreparedSql()
			);

			$entryValuesPrepared = $this->collectEntryValues($entriesValues);
			$results[] = [
				'entry' => $entry,
				'values' => $entryValuesPrepared
			];
		}
		return $results;
	}

	/**
	 * Collect entry values as Caldera_Forms_Entry_Field objects
	 *
	 * @param \stdClass[] $entriesValues
	 * @return array
	 */
	private function collectEntryValues($entriesValues): array
	{
		$entryValuesPrepared = [];
		if (!empty($entriesValues)) {
			foreach ($entriesValues as $entryValue) {
				$entryValuesPrepared[] = new \Caldera_Forms_Entry_Field($entryValue);
			}
		}
		return $entryValuesPrepared;
	}

	/**
	 * Reset entry generator
	 */
	private function resetEntryGenerator()
	{
		$this->entryGenerator->resetQuery();
	}

	/**
	 * Reset entry value generator
	 */
	private function resetEntryValueGenerator()
	{
		$this->entryValueGenerator->resetQuery();
	}
}
