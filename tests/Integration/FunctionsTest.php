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

	/**
	 * @since  1.8.0
	 *
	 * @covers \caldera_forms_get_v2_container()
	 */
	public function testUpgradesAdvancedFileFields(){
		$id = 'fld1';
		$fieldConfig = [
			'id' => $id,
			'slug' => 'roy',
			'type' => 'advanced_file',
			'conditions' => [
				'type' => 'IS'
			]
		];
		$field = \Caldera_Forms_Field_Util::get_field($id,[
			'fields' => [
				$id => $fieldConfig
			],
			'conditional_groups' => []
		], true);
		$this->assertSame(\calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType::getCf1Identifier(), $field['type'] );
	}
}
