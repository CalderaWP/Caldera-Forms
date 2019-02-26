<?php


namespace calderawp\CalderaFormsQuery\Tests\Unit\Delete;

use calderawp\CalderaFormsQuery\Delete\DeleteQueryBuilder;
use calderawp\CalderaFormsQuery\MySqlBuilder;
use calderawp\CalderaFormsQuery\Tests\Unit\TestCase;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;

class DeleteQueryBuilderTest extends TestCase
{

	/**
	 * Test table names
	 *
	 * @covers DeleteQueryBuilder::getTableName()
	 * @covers DeleteQueryBuilder::$tableName
	 */
	public function testGetTableName()
	{
		$entry = $this->entryDeleteGeneratorFactory();
		$this->assertEquals($this->entryTableName(), $entry->getTableName());

		$entryValues = $this->entryValuesDeleteGeneratorFactory();
		$this->assertSame($this->entryValueTableName(), $entryValues->getTableName());
	}

	/**
	 * Test getting SQL builder
	 *
	 * @covers DeleteQueryBuilder::getBuilder()
	 * @covers DeleteQueryBuilder::$builder
	 */
	public function testGetBuilder()
	{
		$entry = $this->entryDeleteGeneratorFactory();
		$this->assertTrue(is_a($entry->getBuilder(), MySqlBuilder::class));

		$entryValues = $this->entryValuesDeleteGeneratorFactory();
		$this->assertTrue(is_a($entryValues->getBuilder(), MySqlBuilder::class));
	}

	/**
	 * Ensure that getDeleteQuery returns the delete query
	 *
	 * @covers DeleteQueryBuilder::getDeleteQuery()
	 * @covers DeleteQueryBuilder::$deleteQuery
	 */
	public function testGetDeleteQueryReturnsDeleteQuery()
	{
		$entry = $this->entryDeleteGeneratorFactory();
		$this->assertTrue(is_a($entry->getDeleteQuery(), Delete::class));

		$entryValues = $this->entryValuesDeleteGeneratorFactory();
		$this->assertTrue(is_a($entryValues->getDeleteQuery(), Delete::class));
	}

	/**
	 * Ensure deleteQuery and currentQuery are the same
	 *
	 * @covers DeleteQueryBuilder::getDeleteQuery()
	 * @covers DeleteQueryBuilder::getCurrentQuery()
	 */
	public function testGetDeleteQueryAndCurrentQueryAreSame()
	{
		$entry = $this->entryDeleteGeneratorFactory();
		$this->assertSame($entry->getDeleteQuery(), $entry->getCurrentQuery());

		$entryValues = $this->entryValuesDeleteGeneratorFactory();
		$this->assertSame($entryValues->getDeleteQuery(), $entryValues->getCurrentQuery());
	}

	/**
	 * Test table name is set on query builder correctly
	 *
	 * @covers DeleteQueryBuilder::getDeleteQuery()
	 */
	public function testTableNameForQueryBuilder()
	{
		$entry = $this->entryDeleteGeneratorFactory();
		$this->assertEquals(
			$this->entryTableName(),
			$entry
			->getDeleteQuery()
			->getTable()
			->getName()
		);

		$entryValues = $this->entryValuesDeleteGeneratorFactory();
		$this->assertEquals(
			$this->entryValueTableName(),
			$entryValues
				->getDeleteQuery()
				->getTable()
				->getName()
		);
	}

	/**
	 * Test reset of builder
	 *
	 * @covers DeleteQueryBuilder::resetBuilder()
	 * @covers DeleteQueryBuilder::$deleteQuery
	 */
	public function testResetOfBuilder()
	{
		$entryGenerator = $this->entryDeleteGeneratorFactory();
		$newBuilder = new MySqlBuilder();
		$entryGenerator->resetBuilder($newBuilder);
		$this->assertSame($newBuilder, $entryGenerator->getBuilder());
	}

	public function testResetOfQuery()
	{
		$entryGenerator = $this->entryDeleteGeneratorFactory();
		$entryGenerator->deleteByUserId(55);
		$entryGenerator->resetQuery();
		$entryGenerator->deleteByUserId(42);
		$this->assertTrue(is_int(strpos($entryGenerator->getPreparedSql(), '42')));
		$this->assertTrue(! is_int(strpos($entryGenerator->getPreparedSql(), '55')));
	}
}
