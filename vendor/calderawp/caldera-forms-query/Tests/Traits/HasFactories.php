<?php


namespace calderawp\CalderaFormsQuery\Tests\Traits;

use calderawp\CalderaContainers\Service\Container;
use calderawp\CalderaFormsQuery\DeleteQueries;
use calderawp\CalderaFormsQuery\Features\FeatureContainer;
use calderawp\CalderaFormsQuery\SelectQueries;
use calderawp\CalderaFormsQuery\Tests\Unit\Features\QueriesTest;

trait HasFactories
{

	/**
	 * @return \calderawp\CalderaFormsQuery\Select\Entry
	 */
	protected function entryGeneratorFactory()
	{
		return new \calderawp\CalderaFormsQuery\Select\Entry(
			$this->mySqlBuilderFactory(),
			$this->entryTableName()
		);
	}

	/**
	 * @return \calderawp\CalderaFormsQuery\Delete\Entry
	 */
	protected function entryDeleteGeneratorFactory()
	{
		return new \calderawp\CalderaFormsQuery\Delete\Entry(
			$this->mySqlBuilderFactory(),
			$this->entryTableName()
		);
	}


	/**
	 * @return \calderawp\CalderaFormsQuery\Select\EntryValues
	 */
	protected function entryValuesGeneratorFactory()
	{
		return new \calderawp\CalderaFormsQuery\Select\EntryValues(
			$this->mySqlBuilderFactory(),
			$this->entryValueTableName()
		);
	}
	/**
	 * @return \calderawp\CalderaFormsQuery\Delete\EntryValues
	 */
	protected function entryValuesDeleteGeneratorFactory()
	{
		return new \calderawp\CalderaFormsQuery\Delete\EntryValues(
			$this->mySqlBuilderFactory(),
			$this->entryValueTableName()
		);
	}



	/**
	 * @return \calderawp\CalderaFormsQuery\MySqlBuilder
	 */
	protected function mySqlBuilderFactory()
	{
		return new \calderawp\CalderaFormsQuery\MySqlBuilder();
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
	 * @return DeleteQueries
	 */
	protected function deleteQueriesFactory()
	{

		return new DeleteQueries(
			$this->entryDeleteGeneratorFactory(),
			$this->entryValuesDeleteGeneratorFactory(),
			$this->getWPDB()
		);
	}

	/**
	 * @return \calderawp\CalderaFormsQuery\Features\Queries
	 */
	protected function featureQueriesFactory()
	{
		return new \calderawp\CalderaFormsQuery\Features\Queries(
			$this->selectQueriesFactory(),
			$this->deleteQueriesFactory()
		);
	}

	/**
	 * @return FeatureContainer
	 */
	protected function containerFactory()
	{
		return new FeatureContainer(
			new Container(),
			$this->getWPDB()
		);
	}

	/**
	 * Gets a WPDB instance
	 *
	 * @return \wpdb
	 */
	protected function getWPDB()
	{
		global $wpdb;
		if (! class_exists('\WP_User')) {
			include_once dirname(dirname(__FILE__)) . '/Mock/wpdb.php';
		}

		if (! $wpdb) {
			$wpdb = new \wpdb('', '', '', '');
		}
		return $wpdb;
	}

	/**
	 * @return string
	 */
	protected function entryValueTableName(): string
	{
		return "{$this->getWPDB()->prefix}cf_form_entry_values";
	}

	/**
	 * @return string
	 */
	protected function entryTableName(): string
	{
		return "{$this->getWPDB()->prefix}cf_form_entries";
	}
}
