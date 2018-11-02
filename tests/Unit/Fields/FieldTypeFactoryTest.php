<?php

namespace calderawp\calderaforms\Tests\Unit\Fields;

use calderawp\calderaforms\cf2\Fields\FieldTypeFactory;
use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Fields\FieldTypes\TextFieldType;
use calderawp\calderaforms\Tests\Unit\TestCase;
use calderawp\calderaforms\Tests\Util\Mocks\MockFieldHandler;

class FieldTypeFactoryTest extends TestCase
{

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\FieldTypeFactory::get()
	 */
	public function testGet ()
	{
		$fieldTypeFactory = new FieldTypeFactory();
		$fieldTypeFactory->add(new FileFieldType );
		$this->assertInstanceOf( FileFieldType::class,$fieldTypeFactory->get(FileFieldType::getCf1Identifier()));
	}
	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\FieldTypeFactory::getAll()
	 */
	public function testGetAll ()
	{
		$fieldTypeFactory = new FieldTypeFactory();
		$fieldTypeFactory->add(new TextFieldType );
		$fieldTypeFactory->add(new FileFieldType() );
		$this->assertCount(2, $fieldTypeFactory->getAll() );


	}
	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\FieldTypeFactory::has()
	 */
	public function testHas ()
	{
		$fieldTypeFactory = new FieldTypeFactory();
		$this->assertFalse($fieldTypeFactory->has( FileFieldType::getCf1Identifier() ) );
		$fieldTypeFactory->add(new FileFieldType );
		$this->assertTrue($fieldTypeFactory->has( FileFieldType::getCf1Identifier() ) );

	}
	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\FieldTypeFactory::add()
	 */
	public function testAdd ()
	{
		$fieldTypeFactory = new FieldTypeFactory();
		$fieldTypeFactory->add(new TextFieldType );
		$this->assertAttributeCount(1,'fields', $fieldTypeFactory );
		$fieldTypeFactory->add(new TextFieldType );
		$this->assertAttributeCount(1,'fields', $fieldTypeFactory );


	}
	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\FieldTypeFactory::add()
	 */
	public function testAddMultiple ()
	{
		$fieldTypeFactory = new FieldTypeFactory();
		$fieldTypeFactory->add(new TextFieldType );
		$fieldTypeFactory->add(new FileFieldType() );
		$this->assertTrue( $fieldTypeFactory->has( TextFieldType::getCf1Identifier() ) );
		$this->assertTrue( $fieldTypeFactory->has( FileFieldType::getCf1Identifier() ) );

	}
}
