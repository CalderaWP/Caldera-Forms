<?php


namespace calderawp\CalderaFormsQuery;

/**
 * Interface CreatesSqlQueries
 *
 * Interface that all classes that create SQL queries MUST impliment
 */
interface CreatesSqlQueries
{

	/**
	 * Get name of table being queried
	 *
	 * @return string
	 */
	public function getTableName();

	/**
	 * Get usable SQL statement from query builder
	 *
	 * @return string
	 */
	public function getPreparedSql();

	/**
	 * Get query builder instance
	 *
	 * @return MySqlBuilder
	 */
	public function getBuilder();

	/**
	 * Reset the query builder
	 *
	 * @param MySqlBuilder|null $builder New builder or null to use default empty
	 * @return $this
	 */
	public function resetBuilder(MySqlBuilder $builder = null);

	/**
	 * Reset the query
	 *
	 * @return $this
	 */
	public function resetQuery();
}
