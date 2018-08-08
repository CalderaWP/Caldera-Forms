<?php


namespace calderawp\CalderaFormsQuery;

use calderawp\CalderaFormsQuery\Delete\Entry;
use calderawp\CalderaFormsQuery\Delete\EntryValues;

class DeleteQueries implements CreatesDeleteQueries
{

	/**
	 * SQL generator for entry table
	 *
	 * @var Entry
	 */
	protected $entryGenerator;

	/**
	 * SQL generator for entry values table
	 *
	 * @var EntryValues
	 */
	protected $entryValueGenerator;


	/**
	 * @var \wpdb
	 */
	protected $wpdb;

	public function __construct(Entry $entryGenerator, EntryValues $entryValueGenerator, \wpdb $wpdb)
	{
		$this->entryGenerator = $entryGenerator;
		$this->entryValueGenerator = $entryValueGenerator;
		$this->wpdb = $wpdb;
	}

	/** @inheritdoc */
	public function getResults($sql)
	{
		$results = $this->wpdb->get_results($sql);
		if (empty($results)) {
			return [];
		}
		return $results;
	}

	/** @inheritdoc */
	public function getEntryValueGenerator()
	{
		return $this->entryValueGenerator;
	}

	/** @inheritdoc */
	public function getEntryGenerator()
	{
		return $this->entryGenerator;
	}
}
