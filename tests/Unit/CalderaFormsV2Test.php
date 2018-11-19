<?php

namespace calderawp\calderaforms\Tests\Unit;

use calderawp\calderaforms\cf2\CalderaFormsV2;
use calderawp\calderaforms\cf2\Fields\FieldTypeFactory;
use calderawp\calderaforms\cf2\Hooks;
use calderawp\calderaforms\cf2\Services\QueueService;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\Tests\Util\Mocks\MockService;

class CalderaFormsV2Test extends TestCase
{
	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::getTransientsApi();
	 */
	public function testGetTransientsApi ()
	{
		$containerMock = $this->getContainer();
		$this->assertInstanceOf(Cf1TransientsApi::class, $containerMock->getTransientsApi());
		$containerReal = new CalderaFormsV2();
		$this->assertInstanceOf(Cf1TransientsApi::class, $containerReal->getTransientsApi());


	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::getHooks();
	 */
	public function testGetHooks ()
	{
		$containerMock = $this->getContainer();
		$this->assertInstanceOf(Hooks::class, $containerMock->getHooks());
		$containerReal = new CalderaFormsV2();
		$this->assertInstanceOf(Hooks::class, $containerReal->getHooks());
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::getFieldTypeFactory();
	 */
	public function testGetFieldTypeFactory ()
	{
		$containerMock = $this->getContainer();
		$this->assertInstanceOf(FieldTypeFactory::class, $containerMock->getFieldTypeFactory());
		$containerReal = new CalderaFormsV2();
		$this->assertInstanceOf(FieldTypeFactory::class, $containerReal->getFieldTypeFactory());

	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::setCoreDir();
	 */
	public function testSetCorDir(){
		$coreDir = 'foo/bar';
		$containerMock = $this->getContainer();
		$containerMock->setCoreDir($coreDir);
		$this->assertAttributeEquals( $coreDir, 'coreDirPath', $containerMock );
		$containerReal = new CalderaFormsV2();
		$containerReal->setCoreDir($coreDir);
		$this->assertAttributeEquals( $coreDir, 'coreDirPath', $containerReal );
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::getCoreDir();
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::$coreDirPath;
	 */
	public function testGetCorDir(){
		$coreDir = 'foo/bar';
		$containerMock = $this->getContainer();
		$containerMock->setCoreDir($coreDir);
		$this->assertEquals( $coreDir, $containerMock->getCoreDir() );
		$containerReal = new CalderaFormsV2();
		$containerReal->setCoreDir($coreDir);
		$this->assertEquals( $coreDir, $containerReal->getCoreDir() );
	}


	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::setCoreUrl();
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::$coreUrl
	 */
	public function testSetCorUrl(){
		$coreUrl = 'https://example.com/wp-content/plugins/foo';
		$containerMock = $this->getContainer();
		$containerMock->setCoreUrl($coreUrl);
		$this->assertAttributeEquals( $coreUrl, 'coreUrl', $containerMock );
		$containerReal = new CalderaFormsV2();
		$containerReal->setCoreUrl($coreUrl);
		$this->assertAttributeEquals( $coreUrl, 'coreUrl', $containerReal );
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::setCoreUrl();
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::$coreUrl
	 */
	public function testGetCorUrl(){
		$coreUrl = 'https://example.com/wp-content/plugins/foo';
		$containerMock = $this->getContainer();
		$containerMock->setCoreUrl($coreUrl);
		$this->assertEquals( $coreUrl, $containerMock->getCoreUrl() );
		$containerReal = new CalderaFormsV2();
		$containerReal->setCoreUrl($coreUrl);
		$this->assertEquals( $coreUrl, $containerReal->getCoreUrl() );
	}


	/**
	 * Test service registration
	 *
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::registerService();
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::getService();
	 */
	public function testRegisterService(){
		$coreDir = 'foo/bar';
		$containerMock = $this->getContainer();
		$containerMock->setCoreDir($coreDir);

		$service = new MockService();
		$containerMock->registerService($service,true );
		$this->assertInstanceOf( \stdClass::class, $containerMock->getService(MockService::class) );
	}
}
