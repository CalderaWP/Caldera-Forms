<?php


namespace calderawp\calderaforms\Tests\Integration;


use calderawp\calderaforms\cf2\CalderaFormsV2;

class FunctionsTest extends TestCase
{

	/**
	 * @since  1.8.0
	 *
	 * @covers \caldera_forms_get_v2_container()
	 */
	public function testContainerFunctionReturnsSameInstance(){

		$this->assertSame(caldera_forms_get_v2_container(),caldera_forms_get_v2_container() );
	}

	/**
	 * @since  1.8.0
	 *
	 * @covers \caldera_forms_get_v2_container()
	 */
	public function testContainerFunctionReturnsInstance(){

		$this->assertInstanceOf(CalderaFormsV2::class,caldera_forms_get_v2_container() );
	}

	/**
	 * @since  1.8.0
	 *
	 * @covers \caldera_forms_get_v2_container()
	 */
	public function testContainerGetsSetupOnFirstCall(){
		$container = caldera_forms_get_v2_container();
		$this->assertTrue( is_object( $container->getService(  \calderawp\calderaforms\cf2\Services\QueueService::class)));
	}
}
