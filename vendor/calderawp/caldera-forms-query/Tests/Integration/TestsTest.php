<?php


namespace calderawp\CalderaFormsQuery\Tests\Integration;

use calderawp\CalderaFormsQuery\SelectQueries;
use calderawp\CalderaFormsQuery\Select\Entry;
use calderawp\CalderaFormsQuery\Select\EntryValues;

/**
 * Class TestsTest
 *
 * Tests to ensure integration test environment is working
 * @package calderawp\CalderaFormsQuery\Tests\Integration
 */
class TestsTest extends IntegrationTestCase
{
	//Using this so we can test that CF's testing traits are available
	use \Caldera_Forms_Has_Mock_Form;

	/**
	 * Check that Caldera Forms is usable
	 */
	public function testCalderaFormsIsInstalled()
	{
		$this->assertTrue( defined( 'CFCORE_VER' ) );
		$this->assertTrue( class_exists( '\Caldera_Forms' ) );
	}

	/**
	 * Make sure the trait worked
	 */
	public function testMockForm()
	{
		$this->set_mock_form();
		$this->assertTrue( is_array( $this->mock_form  ) );
	}

	/**
	 * Test that factories work for integration tests
	 *
	 * @covers HasFactories::selectQueriesFactory()
	 * @covers HasFactories::entryValuesGeneratorFactory()
	 * @covers HasFactories::entryGeneratorFactory()
	 */
	public function testFactory()
	{
		$this->assertTrue(is_a($this->selectQueriesFactory(), SelectQueries::class));
		$this->assertTrue(is_a($this->entryValuesGeneratorFactory(), EntryValues::class));
		$this->assertTrue(is_a($this->entryGeneratorFactory(), Entry::class));

	}

}