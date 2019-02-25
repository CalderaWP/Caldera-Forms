<?php


namespace calderawp\CalderaFormsQuery\Tests\Unit;

use calderawp\CalderaFormsQuery\Delete\Entry;
use calderawp\CalderaFormsQuery\Delete\EntryValues;

class DeleteQueriesTest extends TestCase
{

	/**
	 * Test getting entry SQL generator
	 *
	 * @covers DeleteQueries::getEntryGenerator()
	 * @covers DeleteQueries::$entryGenerator
	 */
	public function testGetEntryGenerator()
	{
		$queries = $this->deleteQueriesFactory();
		$this->assertTrue(is_a($queries->getEntryGenerator(), Entry::class));
	}

	/**
	 * Test getting entry values SQL generator
	 *
	 * @covers DeleteQueries::getEntryValueGenerator()
	 * @covers DeleteQueries::$entryValueGenerator
	 */
	public function testGetEntryValueGenerator()
	{
		$queries = $this->deleteQueriesFactory();
		$this->assertTrue(is_a($queries->getEntryValueGenerator(), EntryValues::class));
	}

	/**
	 * Test that getResults method returns an array
	 *
	 * @covers DeleteQueries::getResults()
	 */
	public function testGetResults()
	{
		$queries = $this->deleteQueriesFactory();
		$this->assertTrue(is_array($queries->getResults("SELECT `roy` FROM sivan WHERE mike = 'roy'")));
	}
}
