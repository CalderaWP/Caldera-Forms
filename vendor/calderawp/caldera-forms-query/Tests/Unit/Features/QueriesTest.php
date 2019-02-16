<?php
namespace calderawp\CalderaFormsQuery\Tests\Unit\Features;

use calderawp\CalderaFormsQuery\Delete\Entry;
use calderawp\CalderaFormsQuery\Delete\EntryValues;
use calderawp\CalderaFormsQuery\Tests\Unit\TestCase;

class QueriesTest extends TestCase
{
	/**
	 * Test getting entry delete SQL generator
	 *
	 * @covers Queries::entryDelete()
	 */
	public function testGetDeleteEntryGenerator()
	{
		$queries = $this->featureQueriesFactory();
		$this->assertTrue(is_a($queries->entryDelete(), Entry::class));
	}

	/**
	 * Test getting entry delete values SQL generator
	 *
	 * @covers Queries::entryValueDelete()
	 */
	public function testGetDeleteEntryValueGenerator()
	{
		$queries = $this->featureQueriesFactory();
		$this->assertTrue(is_a($queries->entryValueDelete(), EntryValues::class));
	}
	/**
	 * Test getting entry select SQL generator
	 *
	 * @covers Queries::entrySelect()
	 */
	public function testGetSelectEntryGenerator()
	{
		$queries = $this->featureQueriesFactory();
		$this->assertTrue(is_a($queries->entrySelect(), \calderawp\CalderaFormsQuery\Select\Entry::class));
	}

	/**
	 * Test getting entry values  select SQL generator
	 *
	 * @covers Queries::entryValuesSelect()
	 */
	public function testGetSelectEntryValueGenerator()
	{
		$queries = $this->featureQueriesFactory();
		$this->assertTrue(is_a($queries->entryValuesSelect(), \calderawp\CalderaFormsQuery\Select\EntryValues::class));
	}
}
