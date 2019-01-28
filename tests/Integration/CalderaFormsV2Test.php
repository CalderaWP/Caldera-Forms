<?php

namespace calderawp\calderaforms\Tests\Integration;

use calderawp\calderaforms\cf2\CalderaFormsV2;
use calderawp\calderaforms\cf2\Fields\FieldTypeFactory;
use calderawp\calderaforms\cf2\Hooks;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;

class CalderaFormsV2Test extends TestCase
{


	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::getCoreDir();
	 */
	public function testGetCorDirUsesConstantIfPathNotSet(){
		$containerMock = $this->getContainer();
		$this->assertEquals( CFCORE_PATH, $containerMock->getCoreDir() );
		$containerReal = new CalderaFormsV2();
		$this->assertEquals( CFCORE_PATH, $containerReal->getCoreDir() );
	}




	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::getCoreDir();
	 */
	public function testGetCorDirDoesNotUseConstantIfPathSet(){
		$coreDir = 'foo/bar';
		$containerMock = $this->getContainer();
		$containerMock->setCoreDir($coreDir);
		$this->assertEquals( $coreDir, $containerMock->getCoreDir() );
		$containerReal = new CalderaFormsV2();
		$containerReal->setCoreDir($coreDir);
		$this->assertEquals( $coreDir, $containerReal->getCoreDir() );
	}
}
