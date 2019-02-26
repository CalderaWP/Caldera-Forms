<?php


namespace calderawp\CalderaFormsQuery\Tests\Integration;

use calderawp\CalderaFormsQuery\SelectQueries;
use calderawp\CalderaFormsQuery\Tests\Traits\HasFactories;
use calderawp\CalderaFormsQuery\Tests\Traits\UsersMockFormAsDBForm;

/**
 * Class IntegrationTestCase
 *
 * All integration tests MUST extend this class
 *
 * @package CalderaLearn\RestSearch\Tests\Integration
 */
abstract class IntegrationTestCase extends \WP_UnitTestCase
{
	use \Caldera_Forms_Has_Data, HasFactories;

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

	/** @inheritdoc */
	public function tearDown()
	{
		//Delete entries
		$this->deleteAllEntriesForMockForm();
		//Delete all forms
		$forms = \Caldera_Forms_Forms::get_forms();
		if (!empty($forms)) {
			foreach ($forms as $form_id => $config) {
				\Caldera_Forms_Forms::delete_form($form_id);
			}
		}

		parent::tearDown();
	}

	/**
	 * Gets a WPDB instance
	 *
	 * @return \wpdb
	 */
	protected function getWPDB()
	{
		global $wpdb;
		return $wpdb;
	}

	/**
	 * @return SelectQueries
	 */
	protected function selectQueriesFactory()
	{

		return new SelectQueries(
			$this->entryGeneratorFactory(),
			$this->entryValuesGeneratorFactory(),
			$this->getWPDB()
		);
	}


	/**
	 * Use $wpdb->get_results() to do a SQL query directly.
	 *
	 * @param $sql
	 * @return object|null
	 */
	protected function queryWithWPDB( $sql )
	{
		global  $wpdb;
		return $wpdb->get_results( $sql );
	}

	/**
	 *
	 */
	protected function deleteAllEntriesForMockForm()
	{
		$this->entryDeleteGeneratorFactory()->deleteByFormId($this->mock_form_id);
	}

	/**
	 * @return array
	 */
	protected function createEntryWithMockForm()
	{
		return $this->create_entry( $this->mock_form );
	}

	/**
	 * @return int
	 */
	protected function createEntryWithMockFormAndGetEntryId()
	{
		$details = $this->create_entry( $this->mock_form );
		return $details[ 'id' ];
	}




}
