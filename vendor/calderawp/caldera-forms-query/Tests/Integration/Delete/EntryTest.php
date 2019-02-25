<?php


namespace calderawp\CalderaFormsQuery\Tests\Integration\Delete;


use calderawp\CalderaFormsQuery\Delete\Entry;
use calderawp\CalderaFormsQuery\Tests\Integration\IntegrationTestCase;

class EntryTest extends IntegrationTestCase
{


	/**
	 * Test deleting by entry ID
	 *
	 * @covers Entry::deleteByFormId()
	 */
	public function testDeleteByFormId()
	{
		//Create three entries
		$this->createEntryWithMockForm();
		$this->createEntryWithMockForm();
		$this->createEntryWithMockForm();

		//Delete all entries for this form Id
		$this->queryWithWPDB(
			$this
			->entryDeleteGeneratorFactory()
			->deleteByFormId($this->mock_form_id)
			->getPreparedSql()
		);

		//Prepare SQL to query for entries
		$entryGenerator = $this->entryGeneratorFactory();
		$entryGenerator->queryByFormsId($this->mock_form_id);
		$sql = $entryGenerator->getPreparedSql();

		//No entries -> No results
		$this->assertSame([], $this->queryWithWPDB($sql));

	}

	/**
	 * Test we can delete the right entry ID without effecting other entries
	 *
	 * @covers Delete::deleteByEntryId()
	 */
	public function testDeleteByEntryId()
	{
		//Create two entries
		$entryToDeleteDetails = $this->createEntryWithMockForm();
		$entryNotToDeleteDetails = $this->createEntryWithMockForm();

		//Delete one of the new entries
		$this->queryWithWPDB(
			$this
				->entryDeleteGeneratorFactory()
				->deleteByEntryId($entryToDeleteDetails['id'])
				->getPreparedSql()
		);


		//Prepare SQL to query for the entry that was deleted.
		$entryGenerator = $this->entryGeneratorFactory();
		$entryGenerator->queryByEntryId($entryToDeleteDetails['id']);
		$sql = $entryGenerator->getPreparedSql();

		//No entries -> No results
		$this->assertSame([], $this->queryWithWPDB($sql));

		//Prepare SQL to query for the entry that was Not deleted.
		$entryGenerator = $this->entryGeneratorFactory();
		$entryGenerator->queryByEntryId($entryNotToDeleteDetails['id']);
		$sql = $entryGenerator->getPreparedSql();

		//One result: This entry should NOT have been deleted
		$this->assertEquals(1, count($this->queryWithWPDB($sql)));
	}

	/**
	 * Test we can delete entries by user ID
	 *
	 * @covers Delete::deleteByUserId()
	 */
	public function testDeleteByUserId()
	{
		//Create two entries for not logged in user
		$entryDetailsNotLoggedInOne = $this->create_entry($this->mock_form);
		$entryDetailsNotLoggedInTwo = $this->create_entry($this->mock_form);

		//Create two entries for logged in user
		$this->factory()->user->create();
		$userId = $this->factory()->user->create();
		wp_set_current_user($userId);
		$this->assertEquals($userId, get_current_user_id());
		$entryDetailsLoggedInOne = $this->create_entry($this->mock_form);
		$entryDetailsLoggedInTwo = $this->create_entry($this->mock_form);
		$entryGeneratorLoggedIn = $this->entryGeneratorFactory();
		$entryGeneratorLoggedIn->queryByEntryId($entryDetailsLoggedInOne['id']);
		$sql = $entryGeneratorLoggedIn->getPreparedSql();

		//Delete the entries for logged in user and make sure those entries are gone
		$this->queryWithWPDB(
			$this->entryDeleteGeneratorFactory()
			->deleteByUserId($userId)
			->getPreparedSql()
		);

		//Check first entry was deleted
		$entryGeneratorLoggedIn = $this->entryGeneratorFactory();
		$entryGeneratorLoggedIn->queryByEntryId($entryDetailsLoggedInOne['id']);
		$sql = $entryGeneratorLoggedIn->getPreparedSql();
		$results = $this->queryWithWPDB($sql);
		$this->assertSame(0, count($results));

		//Check second entry was also deleted
		$entryGeneratorLoggedIn = $this->entryGeneratorFactory();
		$entryGeneratorLoggedIn->queryByEntryId($entryDetailsLoggedInTwo['id']);
		$sql = $entryGeneratorLoggedIn->getPreparedSql();
		$results = $this->queryWithWPDB($sql);
		$this->assertSame(0, count($results));

		//Check that we get no result for querying by entries of the user ID we just deleted
		$entryGenerator = $this->entryGeneratorFactory();
		$this->assertSame(0, count(
			$this->queryWithWPDB(
				$entryGenerator
					->queryByUserId($userId)
					->getPreparedSql()
			)
		));

		//Test entries from non-logged in user is still there
		$entryGenerator = $this->entryGeneratorFactory();
		$this->assertSame(1, count(
			$this->queryWithWPDB(
				$entryGenerator
					->queryByEntryId($entryDetailsNotLoggedInOne['id'])
					->getPreparedSql()
			)
		));

		$entryGenerator = $this->entryGeneratorFactory();
		$this->assertSame(1, count(
			$this->queryWithWPDB(
				$entryGenerator
					->queryByEntryId($entryDetailsNotLoggedInTwo['id'])
					->getPreparedSql()
			)
		));


		$entryGenerator = $this->entryGeneratorFactory();
		$this->assertSame(2, count(
			$this->queryWithWPDB(
				$entryGenerator
					->queryByUserId(0)
					->getPreparedSql()
			)
		));

	}

	/**
	 * Test querying by IDs
	 *
	 * @covers Entry::deleteByEntryIds()
	 */
	public function testByEntryIds()
	{
		$entryIdOne = $this->createEntryWithMockFormAndGetEntryId();
		$entryIdTwo = $this->createEntryWithMockFormAndGetEntryId();
		$entryIdThree = $this->createEntryWithMockFormAndGetEntryId();

		//Delete results IN One and Three
		$entryGenerator = $this->entryDeleteGeneratorFactory();
		$sql = $entryGenerator->deleteByEntryIds( [
			$entryIdOne,
			$entryIdThree
		])
			->getPreparedSql();
		$this->queryWithWPDB($sql);

		//Query for entry Two expect 1 result
		$entryGenerator = $this->entryGeneratorFactory();
		$sql = $entryGenerator->queryByEntryId($entryIdTwo)
			->getPreparedSql();
		$results = $this->queryWithWPDB( $sql );
		$this->assertSame( 1, count( $results ) );

		//Query for entry One expect 0 result
		$entryGenerator = $this->entryGeneratorFactory();
		$sql = $entryGenerator->queryByEntryId($entryIdOne)
			->getPreparedSql();
		$results = $this->queryWithWPDB( $sql );
		$this->assertSame( 0, count( $results ) );

	}

}