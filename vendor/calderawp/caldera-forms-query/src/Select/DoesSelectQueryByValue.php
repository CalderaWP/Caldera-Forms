<?php


namespace calderawp\CalderaFormsQuery\Select;

/**
 * Interface DoesSelectQueryByValue
 *
 * Interface that select query classes that query by field value MUST Impliment
 */
interface DoesSelectQueryByValue
{

	/**
	 * Create query for entry values with a field whose value equals, doesn't equal or is like (SQL LIKE) a value
	 *
	 * @param string $fieldSlug Field slug
	 * @param string $fieldValue Field value
	 * @param string $type Optional. Type of comparison. Values: equals|notEquals|like Default: 'equals'
	 * @param string $whereOperator Optional. Type of where. Default is 'AND'. Any valid WHERE operator is accepted
	 * @param array $columns Optional. Array of columns to select. Leave empty to select *
	 * @return $this
	 */
	public function queryByFieldValue($fieldSlug, $fieldValue, $type = 'equals', $whereOperator = 'AND', $columns = []);

	/**
	 * Get column name for value lookups
	 *
	 * @return string
	 */
	public function getValueColumn();
}
