<?php


namespace calderawp\CalderaFormsQuery\Tests\Integration\Select;


use calderawp\CalderaFormsQuery\Tests\Integration\IntegrationTestCase;
use calderawp\CalderaFormsQuery\Tests\Traits\HasFactories;
use calderawp\CalderaFormsQuery\Tests\Traits\UsersMockFormAsDBForm;

class EntryTest extends IntegrationTestCase
{

	/** @inheritdoc */
	protected $mock_form_id;
	/** @inheritdoc */
	protected $mock_form;
	/** @inheritdoc */


	/**
	 * Test query by form ID
	 *
	 * @covers Entry::queryByFormsId()
	 */
	public function testQueryByFormsId()
	{
		$entryGenerator = $this->entryGeneratorFactory();
		$entryGenerator->queryByFormsId($this->mock_form_id);
		$sql = $entryGenerator->getPreparedSql();

		//No entries -> No results
		$this->assertSame([], $this->queryWithWPDB( $sql) );


		//One entry -> One result, with the right form ID.
		$this->createEntryWithMockForm();
		$results = $this->queryWithWPDB( $sql);
		$this->assertTrue( ! empty( $this->queryWithWPDB( $sql) ) );
		$this->assertSame( 1, count($results));
		$this->assertSame( $results[0]->form_id, $this->mock_form_id );

		//Two entries -> Two result, with the right form ID.
		$this->createEntryWithMockForm();
		$results = $this->queryWithWPDB( $sql);
		$this->assertTrue( ! empty( $this->queryWithWPDB( $sql) ) );
		$this->assertSame( 2, count($results));
		$this->assertSame( $results[0]->form_id, $this->mock_form_id );
		$this->assertSame( $results[1]->form_id, $this->mock_form_id );

	}

	/**
	 * Test query by entry ID
	 *
	 * @covers Entry::queryByEntryId()
	 */
	public function testQueryByEntryId()
	{
		$entryId = $this->createEntryWithMockFormAndGetEntryId();
		$entryGenerator = $this->entryGeneratorFactory();
		//No results for a non-existent entry
		$entryGenerator->queryByEntryId(42);
		$sql = $entryGenerator->getPreparedSql();
		$this->assertSame( [],  $this->queryWithWPDB($sql));

		//One entry: one result with the correct ID
		$entryGenerator = $this->entryGeneratorFactory();
		$entryGenerator->queryByEntryId($entryId);
		$sql = $entryGenerator->getPreparedSql();
		$results =  $this->queryWithWPDB($sql);
		$this->assertTrue( ! empty( $results ));
		$this->assertSame( 1, count( $results ) );
		$this->assertEquals( $results[0]->id, $entryId);

		//Two more entries: one result for original entry ID
		$this->createEntryWithMockForm();
		$this->createEntryWithMockForm();
		$entryGenerator = $this->entryGeneratorFactory();
		$entryGenerator->queryByEntryId($entryId);
		$sql = $entryGenerator->getPreparedSql();
		$results =  $this->queryWithWPDB($sql);
		$this->assertTrue( ! empty( $results ));
		$this->assertSame( 1, count( $results ) );
	}

	/**
	 * Test querying by IDs
	 *
	 * @covers Entry::queryByEntryIds()
	 */
	public function testByEntryIds()
	{
		$entryIdOne = $this->createEntryWithMockFormAndGetEntryId();
		$entryIdTwo = $this->createEntryWithMockFormAndGetEntryId();
		$entryIdThree = $this->createEntryWithMockFormAndGetEntryId();

		//Two results when asking for IN One and Three
		$entryGenerator = $this->entryGeneratorFactory();
		$sql = $entryGenerator->queryByEntryIds( [
				$entryIdOne,
				$entryIdThree
			])
			->getPreparedSql();
		$results = $this->queryWithWPDB( $sql );
		$this->assertSame( 2, count( $results ) );

		//One results when asking for IN Two
		$entryGenerator = $this->entryGeneratorFactory();
		$sql = $entryGenerator->queryByEntryIds( [
			$entryIdTwo
		])
			->getPreparedSql();
		$results = $this->queryWithWPDB( $sql );
		$this->assertSame( 1, count( $results ) );


	}

	/**
	 * Test query by user ID
	 *
	 * @covers Entry::queryByUserId()
	 */
	/**
	 * Test query by user ID
	 *
	 * @covers Entry::queryByUserId()
	 */
	public function testQueryByUserId()
	{

		//Create an entry for without a user
		$this->deleteAllEntriesForMockForm();
		$entryDetailsNotLoggedIn = $this->create_entry($this->mock_form);

		//Create an entry for a known user.
		$this->factory()->user->create();
		$userId = $this->factory()->user->create();
		wp_set_current_user( $userId );
		$this->assertEquals( $userId, get_current_user_id() );
		$entryDetailsLoggedIn = $this->create_entry($this->mock_form);

		//Make sure there is one entry with with this user
		$entryGeneratorLoggedIn = $this->entryGeneratorFactory();
		$entryGeneratorLoggedIn->queryByEntryId($entryDetailsLoggedIn['id']);
		$sql= $entryGeneratorLoggedIn->getPreparedSql();
		$resultsByEntryId =  $this->queryWithWPDB($sql);
		$this->assertTrue( ! empty( $resultsByEntryId ));
		$this->assertSame( 1, count( $resultsByEntryId ) );
		$this->assertEquals( $userId, $resultsByEntryId[0]->user_id );

		//Test that query by User ID gets the right entry
		$entryGeneratorLoggedIn = $this->entryGeneratorFactory();
		$entryGeneratorLoggedIn->queryByUserId($userId);
		$sql= $entryGeneratorLoggedIn->getPreparedSql();
		$resultsByUserId =  $this->queryWithWPDB($sql);
		$this->assertEqualSets((array)$resultsByEntryId[0], (array)$resultsByUserId[0]);

		//Test that non-logged in user is tracked in DB as 0, not some actual user ID and we can select that way
		$entryGeneratorNotLoggedIn = $this->entryGeneratorFactory();
		$entryGeneratorNotLoggedIn->queryByEntryId($entryDetailsNotLoggedIn['id'] );
		$sql= $entryGeneratorLoggedIn->getPreparedSql();
		$resultsByEntryId =  $this->queryWithWPDB($sql);
		$this->assertTrue( ! empty( $resultsByEntryId ));
		$this->assertSame( 1, count( $resultsByEntryId ) );
		$this->assertEquals( $userId, $resultsByEntryId[0]->user_id );

	}








}