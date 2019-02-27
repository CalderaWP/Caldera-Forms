<?php


namespace calderawp\CalderaFormsQuery\Tests\Unit;

use calderawp\CalderaFormsQuery\MySqlBuilder;

class MySqlBuilderTest extends TestCase
{

	/**
	 * Test that sprintf tags, not :v(n) is used for substitutions
	 *
	 * @covers MySqlBuilder::$placeholderWriter
	 * @covers MySqlBuilder::setPlaceHolderWriter()
	 * @covers MySqlBuilder::__construct()
	 */
	public function testSubstitutions()
	{

		$builder = $this->mySqlBuilderFactory();
		$query = new \NilPortugues\Sql\QueryBuilder\Manipulation\Select('foo');

		$query
			->where()
			->equals('mike', 'roy')
		;

		$builder->write($query);
		$values = $builder->getValues();
		$this->assertTrue(is_array($values));
		$this->assertArrayHasKey('%1s', $values);
	}
}
