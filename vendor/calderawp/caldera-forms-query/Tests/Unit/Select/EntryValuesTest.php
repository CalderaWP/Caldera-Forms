<?php


namespace calderawp\CalderaFormsQuery\Tests\Unit\Select;

use calderawp\CalderaFormsQuery\Tests\Unit\TestCase;

class EntryValuesTest extends TestCase
{

	/**
	 * Test query by field where field value equals a value
	 *
	 * @covers \calderawp\CalderaFormsQuery\Select\EntryValues::queryByFieldValue()
	 */
	public function testQueryByFieldValueEquals()
	{
		$expectedSql = "SELECT `{$this->entryValueTableName()}`.* FROM `{$this->entryValueTableName()}` WHERE (`{$this->entryValueTableName()}`.`value` = 'josh@calderawp.com') AND (`{$this->entryValueTableName()}`.`slug` = 'email_address')";

		$entryValues = $this->entryValuesGeneratorFactory();
		$generator = $entryValues->queryByFieldValue('email_address', 'josh@calderawp.com');
		$this->assertTrue($this->isAEntryValues($generator));

		$actualSql = $entryValues->getPreparedSql();
		$this->assertEquals($expectedSql, $actualSql);
	}

	/**
	 * Test query by field where field value does not equals a value
	 *
	 * @covers \calderawp\CalderaFormsQuery\Select\EntryValues::queryByFieldValue()
	 */
	public function testQueryByFieldValueNotEquals()
	{
		$expectedSql = "SELECT `{$this->entryValueTableName()}`.* FROM `{$this->entryValueTableName()}` WHERE (`{$this->entryValueTableName()}`.`value` <> 'josh@calderawp.com') AND (`{$this->entryValueTableName()}`.`slug` = 'email_address')";
		$entryValues = $this->entryValuesGeneratorFactory();
		$generator =$entryValues->queryByFieldValue('email_address', 'josh@calderawp.com', 'notEquals');
		$this->assertTrue($this->isAEntryValues($generator));

		$actualSql = $entryValues->getPreparedSql();
		$this->assertEquals($expectedSql, $actualSql);
	}

	/**
	 * Test query by field where field value is like a value
	 *
	 * @cover \calderawp\CalderaFormsQuery\Select\EntryValues::$isLike
	 * @covers \calderawp\CalderaFormsQuery\Select\EntryValues::queryByFieldValue()
	 */
	public function testQueryByFieldValueLike()
	{
		$expectedSql = "SELECT `{$this->entryValueTableName()}`.* FROM `{$this->entryValueTableName()}` WHERE (`{$this->entryValueTableName()}`.`value` LIKE '\%josh@calderawp.com\%')";
		
		$entryValues = $this->entryValuesGeneratorFactory();
		$generator = $entryValues->queryByFieldValue('email_address', 'josh@calderawp.com', 'like');
		$this->assertTrue($this->isAEntryValues($generator));

		$actualSql = $entryValues->getPreparedSql();
		$this->assertEquals($expectedSql, $actualSql);
	}

	/**
	 * Test query by entry id
	 *
	 * @covers \calderawp\CalderaFormsQuery\Select\EntryValues::queryByFieldValue()
	 */
	public function testQueryByEntryId()
	{
		$expectedSql = "SELECT `{$this->entryValueTableName()}`.* FROM `{$this->entryValueTableName()}` WHERE (`{$this->entryValueTableName()}`.`entry_id` = 42)";
		$entryValues = $this->entryValuesGeneratorFactory();
		$generator = $entryValues->queryByEntryId(42);
		$this->assertTrue($this->isAEntryValues($generator));

		$actualSql = $entryValues->getPreparedSql();
		$this->assertEquals($expectedSql, $actualSql);
	}

	/**
	 * @param $generator
	 * @return bool
	 */
	protected function isAEntryValues($generator)
	{
		return is_a($generator, '\calderawp\CalderaFormsQuery\Select\EntryValues');
	}
}
