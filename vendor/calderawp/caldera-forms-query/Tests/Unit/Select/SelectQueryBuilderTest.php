<?php


namespace calderawp\CalderaFormsQuery\Tests\Unit\Select;

use calderawp\CalderaFormsQuery\MySqlBuilder;
use calderawp\CalderaFormsQuery\Select\Entry;
use calderawp\CalderaFormsQuery\Select\EntryValues;
use calderawp\CalderaFormsQuery\Select\SelectQueryBuilder;
use calderawp\CalderaFormsQuery\Tests\Unit\TestCase;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

class SelectQueryBuilderTest extends TestCase
{

	/**
	 * Test table names
	 *
	 * @covers SelectQueryBuilder::getTableName()
	 * @covers SelectQueryBuilder::$tableName
	 */
	public function testGetTableName()
	{
		$entry = $this->entryGeneratorFactory();
		$this->assertEquals($this->entryTableName(), $entry->getTableName());

		$entryValues = $this->entryValuesGeneratorFactory();
		$this->assertSame($this->entryValueTableName(), $entryValues->getTableName());
	}

	/**
	 * Test getting SQL builder
	 *
	 * @covers SelectQueryBuilder::getBuilder()
	 * @covers SelectQueryBuilder::$builder
	 */
	public function testGetBuilder()
	{
		$entry = $this->entryGeneratorFactory();
		$this->assertTrue(is_a($entry->getBuilder(), MySqlBuilder::class));

		$entryValues = $this->entryValuesGeneratorFactory();
		$this->assertTrue(is_a($entryValues->getBuilder(), MySqlBuilder::class));
	}

	/**
	 * Test getting select query generator
	 *
	 * @covers SelectQueryBuilder::getSelectQuery()
	 * @covers SelectQueryBuilder::$selectQuery
	 */
	public function testGetSelectQuery()
	{
		$entry = $this->entryGeneratorFactory();
		$this->assertTrue(is_a($entry->getSelectQuery(), Select::class));

		$entryValues = $this->entryValuesGeneratorFactory();
		$this->assertTrue(is_a($entryValues->getSelectQuery(), Select::class));
	}

	/**
	 * Test adding orderby DESC
	 *
	 * @covers SelectQueryBuilder::addOrderBy()
	 */
	public function testAddOrderByDesc()
	{
		$entry = $this->entryGeneratorFactory();
		$expectedSql = "SELECT `{$this->entryTableName()}`.* FROM `{$this->entryTableName()}` WHERE (`{$this->entryTableName()}`.`form_id` = 'cf12345') ORDER BY `{$this->entryTableName()}`.`form_id` DESC";
		$entry->queryByFormsId('cf12345');
		$entry->addOrderBy('form_id', false);
		$actualSql = $entry->getPreparedSql();
		$this->assertEquals($expectedSql, $actualSql);
	}

	/**
	 * Test adding orderby ASC
	 *
	 * @covers SelectQueryBuilder::addOrderBy()
	 */
	public function testAddOrderByAsc()
	{
		$entry = $this->entryGeneratorFactory();
		$expectedSql = "SELECT `{$this->entryTableName()}`.* FROM `{$this->entryTableName()}` WHERE (`{$this->entryTableName()}`.`form_id` = 'cf12345') ORDER BY `{$this->entryTableName()}`.`form_id` ASC";
		$entry->queryByFormsId('cf12345');
		$entry->addOrderBy('form_id');
		$actualSql = $entry->getPreparedSql();
		$this->assertEquals($expectedSql, $actualSql);
	}

	/**
	 * Test the ASC constant
	 *
	 * @covers SelectQueryBuilder::ASC
	 */
	public function testAscConstant()
	{
		$this->assertEquals(Entry::ASC, 'ASC');
		$this->assertEquals(EntryValues::ASC, 'ASC');
	}

	/**
	 * Test the DESC constant
	 *
	 * @covers SelectQueryBuilder::DESC
	 */
	public function testDescConstant()
	{
		$this->assertEquals(Entry::DESC, 'DESC');
		$this->assertEquals(EntryValues::DESC, 'DESC');
	}

	/**
	 * Test reset of builder
	 *
	 * @covers SelectQueryBuilder::resetBuilder()
	 */
	public function testResetOfBuilder()
	{
		$entryGenerator = $this->entryGeneratorFactory();
		$newBuilder = new MySqlBuilder();
		$entryGenerator->resetBuilder($newBuilder);
		$this->assertSame($newBuilder, $entryGenerator->getBuilder());
	}
}
