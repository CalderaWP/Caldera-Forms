<?php


namespace calderawp\CalderaFormsQuery\Features;

use calderawp\CalderaFormsQuery\CreatesDeleteQueries;
use calderawp\CalderaFormsQuery\CreatesSelectQueries;
use calderawp\CalderaFormsQuery\Delete\DeleteQueryBuilder;
use calderawp\CalderaFormsQuery\Delete\DoesDeleteQuery;
use calderawp\CalderaFormsQuery\Delete\Entry as EntryDelete;
use \calderawp\CalderaFormsQuery\Delete\EntryValues as EntryValuesDelete;
use calderawp\CalderaFormsQuery\DeleteQueries;
use calderawp\CalderaFormsQuery\Select\DoesSelectQuery;
use \calderawp\CalderaFormsQuery\Select\Entry as EntrySelect;
use \calderawp\CalderaFormsQuery\Select\EntryValues as EntryValueSelect;
use calderawp\CalderaFormsQuery\Select\SelectQueryBuilder;

class Queries implements DoesQueries
{


	/**
	 * @var CreatesSelectQueries
	 */
	protected $selectQueries;
	/**
	 * @var CreatesDeleteQueries
	 */
	protected $deleteQueries;

	/**
	 * Queries constructor.
	 * @param CreatesSelectQueries $selectQueries
	 * @param CreatesDeleteQueries $deleteQueries
	 */
	public function __construct(CreatesSelectQueries $selectQueries, CreatesDeleteQueries $deleteQueries)
	{
		$this->selectQueries = $selectQueries;
		$this->deleteQueries = $deleteQueries;
	}

	/**
	 * @param SelectQueryBuilder $query
	 * @return \stdClass[]
	 */
	public function select(SelectQueryBuilder $query)
	{
		return $this
			->selectQueries
			->getResults(
				$query->getPreparedSql()
			);
	}

	/**
	 * @param DeleteQueryBuilder $query
	 * @return \stdClass[]
	 */
	public function delete(DeleteQueryBuilder $query)
	{
		return $this
			->deleteQueries
			->getResults(
				$query->getPreparedSql()
			);
	}

	/**
	 * Create a new SELECT query for Entry table
	 *
	 * @return EntrySelect
	 */
	public function entrySelect()
	{
		$this
			->selectQueries
			->getEntryGenerator()
			->resetQuery();
		return $this
			->selectQueries
			->getEntryGenerator();
	}

	/**
	 * Create a new SELECT query for Entry value table
	 *
	 * @return EntryValueSelect
	 */
	public function entryValuesSelect()
	{
		$this
			->selectQueries
			->getEntryValueGenerator()
			->resetQuery();
		return $this
			->selectQueries
			->getEntryValueGenerator();
	}

	/**
	 * Create a new DELETE query for entries
	 *
	 * @return EntryDelete
	 */
	public function entryDelete()
	{
		$this
			->deleteQueries
			->getEntryGenerator()
			->resetQuery();
		return $this
			->deleteQueries
			->getEntryGenerator();
	}

	/**
	 * @return EntryValuesDelete
	 */
	public function entryValueDelete()
	{
		$this
			->deleteQueries
			->getEntryValueGenerator()
			->resetQuery();
		return $this
			->deleteQueries
			->getEntryValueGenerator();
	}
}
