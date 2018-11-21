<?php

namespace calderawp\calderaforms\Tests\Unit\Fields;

use calderawp\calderaforms\cf2\Fields\FieldTypeFactory;
use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Fields\RegisterFields;
use calderawp\calderaforms\Tests\Unit\TestCase;

class RegisterFieldsTest extends TestCase
{

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\RegisterFields::__construct()
	 */
	public function test__construct ()
	{
		$fieldTypeFactory = new FieldTypeFactory();
		$path = 'foo/bar';
		$register = new RegisterFields($fieldTypeFactory, $path);
		$this->assertAttributeEquals($fieldTypeFactory, 'factory', $register);
		$this->assertAttributeEquals($path, 'coreDirPath', $register);
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\RegisterFields::getCoreDirPath()
	 */
	public function testGetCoreDirPath ()
	{
		$fieldTypeFactory = new FieldTypeFactory();
		$path = 'foo/bar';
		$register = new RegisterFields($fieldTypeFactory, $path);
		$this->assertEquals($path, $register->getCoreDirPath());
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\RegisterFields::filter()
	 */
	public function testFilter ()
	{
		$fieldTypeFactory = new FieldTypeFactory();
		$fieldTypeFactory->add(new FileFieldType());
		$path = 'foo/bar';
		$register = new RegisterFields($fieldTypeFactory, $path);
		$filtered = $register->filter([]);
		$this->assertArrayHasKey(FileFieldType::getCf1Identifier(), $filtered);
		$field = $filtered[ FileFieldType::getCf1Identifier() ];
		$this->assertArrayHasKey('setup', $field);
		$this->assertArrayHasKey('cf2', $field);
		$this->assertArrayHasKey('description', $field);
		$this->assertArrayHasKey('field', $field);
		$this->assertTrue($field[ 'cf2' ]);
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\RegisterFields::filter()
	 */
	public function testFilterAddsFullPath ()
	{
		$fieldTypeFactory = new FieldTypeFactory();
		$fieldTypeFactory->add(new FileFieldType());
		$path = 'foo/bar';
		$register = new RegisterFields($fieldTypeFactory, $path);
		$filtered = $register->filter([]);
		$this->assertArrayHasKey(FileFieldType::getCf1Identifier(), $filtered);
		$setup = $filtered[ FileFieldType::getCf1Identifier() ][ 'setup' ];
		$this->assertTrue(0 === strpos($setup[ 'preview' ], $path));
		$this->assertTrue(0 === strpos($setup[ 'template' ], $path));

	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType()
	 */
	public function testFileFieldIcon(){
		$this->assertNotFalse( strpos( FileFieldType::getIcon(), 'cloud-upload.svg') );
	}
}
