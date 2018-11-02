<?php


namespace calderawp\calderaforms\Tests\Unit\Fields;


use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\Tests\Unit\TestCase;

class FieldTypeTest extends TestCase
{

	/**
	 * @covers  \calderawp\calderaforms\cf2\Fields\FieldType::toArray
	 * @covers  \calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType::toArray
	 */
	public function testToArray()
	{
		$array = FileFieldType::toArray();
		$this->assertTrue($array['cf2'] );
		$this->assertArrayHasKey('field',$array );
		$this->assertTrue( is_string( $array['field']));

		$this->assertArrayHasKey('description',$array );
		$this->assertTrue( is_string( $array['description']));

		$this->assertArrayHasKey('category',$array );
		$this->assertTrue( is_string( $array['category']));

		$this->assertArrayHasKey('template', $array['setup'] );
		$this->assertTrue( is_string( $array['setup']['template']));

		$this->assertArrayHasKey('preview',$array['setup'] );
		$this->assertTrue( is_string( $array['setup']['preview']));

	}
}
