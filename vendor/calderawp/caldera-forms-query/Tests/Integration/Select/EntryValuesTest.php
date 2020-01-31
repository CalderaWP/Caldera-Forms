<?php


namespace calderawp\CalderaFormsQuery\Tests\Integration\Select;



use calderawp\CalderaFormsQuery\Tests\Integration\IntegrationTestCase;
use calderawp\CalderaFormsQuery\Tests\Traits\CanCreateEntryWithEmailField;

class EntryValuesTest extends IntegrationTestCase
{
	use CanCreateEntryWithEmailField;

	/**
	 * Test query by entry ID
	 *
	 * @covers EntryValues::queryByEntryId()
	 */
	public function testQueryByEntryId()
	{
		$entry = $this->createEntryWithMockForm();
		$entryValuesQueryGenerator = $this->entryValuesGeneratorFactory();
		$entryValuesQueryGenerator->queryByEntryId($entry['id']);
		$sql = $entryValuesQueryGenerator->getPreparedSql();
		$results =  $this->queryWithWPDB($sql);
		$this->assertTrue( ! empty( $results ));
		$this->assertSame( 4, count( $results ) );
		$this->assertEquals( $results[0]->entry_id, $entry['id']);
		$this->assertEquals( $results[1]->entry_id, $entry['id']);
		$this->assertEquals( $results[2]->entry_id, $entry['id']);
		$this->assertEquals( $results[3]->entry_id, $entry['id']);

	}

	/**
	 * Test query by field where field value equals a value
	 *
	 * @covers \calderawp\CalderaFormsQuery\Select\EntryValues::queryByFieldValue()
	 */
	public function testQueryByFieldValueEquals()
	{
		//Entry with no real email
		$this->createEntryWithMockForm();
		//Create entries for each of two emails
		$emailOne = 'one@hiroy.club';
		$emailTwo = 'two@hiroy.club';
		$this->createEntryWithEmail( $emailOne );
		$this->createEntryWithEmail( $emailTwo );

		//One entry when querying by first email
		$entryValuesQueryGenerator = $this->entryValuesGeneratorFactory();

		$this->assertSame( 1, count(
			$this->queryWithWPDB(
				$entryValuesQueryGenerator
					->queryByFieldValue(
						$this->getEmailFieldSlug(),
						$emailOne
					)
				->getPreparedSql()
			)
		) );

		//One entry when querying by second email
		$entryValuesQueryGenerator = $this->entryValuesGeneratorFactory();
		$this->assertSame( 1, count(
			$this->queryWithWPDB(
				$entryValuesQueryGenerator
					->queryByFieldValue(
						$this->getEmailFieldSlug(),
						$emailTwo
					)
				->getPreparedSql()
			)
		) );

	}

	/**
	 * Test query by field where field does not equals a value
	 *
	 * @covers \calderawp\CalderaFormsQuery\Select\EntryValues::queryByFieldValue()
	 */
	public function testQueryByFieldValueNotEquals()
	{
		//Entry with no real email
		$this->createEntryWithMockForm();
		//Create entries for each of two emails
		$emailOne = 'one@hiroy.club';
		$emailTwo = 'two@hiroy.club';
		$this->createEntryWithEmail( $emailOne );
		$this->createEntryWithEmail( $emailTwo );

		//Two entries when querying by NOT first email
		$entryValuesQueryGenerator = $this->entryValuesGeneratorFactory();
		$this->assertSame( 2, count(
			$this->queryWithWPDB(
				$entryValuesQueryGenerator
					->queryByFieldValue(
						$this->getEmailFieldSlug(),
						$emailOne,
						'notEquals'
					)
				->getPreparedSql()
			)
		) );

		//Two entries when querying by NOT second email
		$entryValuesQueryGenerator = $this->entryValuesGeneratorFactory();
		$this->assertSame( 2, count(
			$this->queryWithWPDB(
				$entryValuesQueryGenerator
					->queryByFieldValue(
						$this->getEmailFieldSlug(),
						$emailTwo,
						'notEquals'
					)
				->getPreparedSql()
			)
		) );

	}

}