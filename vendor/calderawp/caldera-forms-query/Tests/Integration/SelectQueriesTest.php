<?php


namespace calderawp\CalderaFormsQuery\Tests\Integration;


use calderawp\CalderaFormsQuery\SelectQueries;
use calderawp\CalderaFormsQuery\Tests\Traits\CanCreateEntryWithEmailField;
use calderawp\CalderaFormsQuery\Tests\Traits\HasFactories;
use calderawp\CalderaFormsQuery\Tests\Traits\UsersMockFormAsDBForm;

class SelectQueriesTest extends IntegrationTestCase
{
	use UsersMockFormAsDBForm;
	public function setUp()
	{
		global $wpdb;
		$tables = new \Caldera_Forms_DB_Tables($wpdb);
		$tables->add_if_needed();
		$this->set_mock_form();
		$this->mock_form_id = \Caldera_Forms_Forms::import_form( $this->mock_form );
		$this->mock_form = \Caldera_Forms_Forms::get_form( $this->mock_form_id );
		parent::setUp();
	}

	/**
	 * Test that getResults method runs queries against WordPress correctly
	 *
	 * @covers SelectQueries::getResults()
	 */
	public function testGetResultsCanDoSQL(){
		$details = $this->create_entry( $this->mock_form );
		$details = $this->create_entry( $this->mock_form );
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s", $this->mock_form_id );
		$results = $this->selectQueriesFactory()->getResults( $sql );
		$this->assertTrue( ! empty( $results ) );
		$this->assertEquals( 2, $results[0]->total);

		$wpdbDirectResults = $wpdb->get_results( $sql );
		$this->assertEquals( 2, $wpdbDirectResults[0]->total);
	}

	/**
	 * Test that we can run queries and the environment can update DB
	 *
	 * @covers SelectQueries::getResults()
	 */
	public function testCanQuery()
	{
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s", $this->mock_form_id );
		$resultsOne = $this->selectQueriesFactory()->getResults( $sql );
		$this->assertTrue( ! empty( $resultsOne ) );

		$entry_details = $this->create_entry( $this->mock_form );
		$sql = $wpdb->prepare("SELECT * FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `id` = %s", $entry_details['id'] );
		$resultsTwo = $this->selectQueriesFactory()->getResults( $sql );
		$this->assertTrue( ! empty( $resultsTwo ), var_export( $resultsTwo, true ) );

	}


}