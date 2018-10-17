<?php


namespace calderawp\CalderaFormsQuery\Tests\Integration;


use function calderawp\CalderaFormsQueries\CalderaFormsQueries;
use calderawp\CalderaFormsQuery\Features\FeatureContainer;

class FunctionsTest extends IntegrationTestCase
{

	/**
	 * Ensure that accessor function returns the right class type
	 * @covers CalderaFormsQueries()
	 */
	public function testGetMainInstance()
	{
		$this->assertSame( FeatureContainer::class, get_class(CalderaFormsQueries()) );
	}
	/**
	 * Ensure that accessor function returns the same class instance
	 * @covers CalderaFormsQueries()
	 */
	public function testIsSameInstance()
	{
		$this->assertSame( CalderaFormsQueries(), CalderaFormsQueries() );
		CalderaFormsQueries()->set('sivan', 'roy' );
		$this->assertEquals( 'roy', CalderaFormsQueries()->get('sivan') );
	}


}