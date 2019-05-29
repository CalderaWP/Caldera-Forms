<?php


namespace calderawp\CalderaFormsQuery\Delete;

use calderawp\CalderaFormsQuery\QueryBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;

abstract class DeleteQueryBuilder extends QueryBuilder implements DoesDeleteQuery
{

	/**
	 * @var Delete
	 */
	protected $deleteQuery;

	/**
	 * @return Delete
	 */
	public function getDeleteQuery()
	{
		if (! $this->deleteQuery) {
			$this->setNewQuery();
		}

		return $this->deleteQuery;
	}

	/**
	 * @return Delete
	 */
	public function getCurrentQuery()
	{
		return $this->getDeleteQuery();
	}

	/** @inheritdoc */
	public function resetQuery()
	{
		$this->setNewQuery();
	}

	/**
	 * Set a new delete query
	 */
	private function setNewQuery()
	{
		$this->deleteQuery = new Delete($this->getTableName());
	}
}
