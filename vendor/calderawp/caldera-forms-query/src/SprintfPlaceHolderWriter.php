<?php

namespace calderawp\CalderaFormsQuery;

use NilPortugues\Sql\QueryBuilder\Builder\Syntax\PlaceholderWriter;

class SprintfPlaceHolderWriter extends PlaceholderWriter
{

	/** @inheritdoc */
	public function add($value)
	{
		//@todo type detection
		$placeholderKey = '%'.$this->counter.'s';
		$this->placeholders[$placeholderKey] = $this->setValidSqlValue($value);

		++$this->counter;

		return $placeholderKey;
	}
}
