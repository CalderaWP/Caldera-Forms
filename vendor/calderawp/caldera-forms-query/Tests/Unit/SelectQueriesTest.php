<?php


namespace calderawp\CalderaFormsQuery\Tests\Unit;

use calderawp\CalderaFormsQuery\SelectQueries;
use calderawp\CalderaFormsQuery\Select\Entry;
use calderawp\CalderaFormsQuery\Select\EntryValues;
use calderawp\CalderaFormsQuery\Select\SelectQueryBuilder;

class SelectQueriesTest extends TestCase
{

	/**
	 * Test getting entry SQL generator
	 *
	 * @covers SelectQueries::getEntryGenerator()
	 * @covers SelectQueries::$entryGenerator
	 */
	public function testGetEntryGenerator()
	{
		$queries = $this->selectQueriesFactory();
		$this->assertTrue(is_a($queries->getEntryGenerator(), Entry::class));
	}

	/**
	 * Test getting entry values SQL generator
	 *
	 * @covers SelectQueries::getEntryValueGenerator()
	 * @covers SelectQueries::$entryValueGenerator
	 */
	public function testGetEntryValueGenerator()
	{
		$queries = $this->selectQueriesFactory();
		$this->assertTrue(is_a($queries->getEntryValueGenerator(), EntryValues::class));
	}

	/**
	 * Test that getResults method returns an array
	 *
	 * @covers SelectQueries::getResults()
	 */
	public function testGetResults()
	{
		$queries = $this->selectQueriesFactory();
		$this->assertTrue(is_array($queries->getResults("SELECT `roy` FROM sivan WHERE mike = 'roy'")));
	}
}
