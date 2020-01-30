<?php


namespace calderawp\CalderaFormsQuery;

use NilPortugues\Sql\QueryBuilder\Builder\Syntax\PlaceholderWriter;

class MySqlBuilder extends \NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder
{

	/** @inheritdoc */
	public function __construct()
	{
		$this->setPlaceHolderWriter();
	}

	/**
	 * Set or reset the placeholder writer
	 *
	 * @param PlaceholderWriter|null $writer
	 */
	public function setPlaceHolderWriter(PlaceholderWriter $writer = null)
	{
		if ($writer) {
			$this->placeholderWriter = $writer;
		}
		$this->placeholderWriter = new SprintfPlaceHolderWriter();
	}
}
