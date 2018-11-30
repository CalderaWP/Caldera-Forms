<?php

namespace calderawp\calderaforms\Tests\Unit;

use calderawp\calderaforms\cf2\Fields\Handlers\FileFieldHandler;
use calderawp\calderaforms\cf2\Hooks;
use calderawp\calderaforms\cf2\RestApi\File\File;
use calderawp\calderaforms\cf2\CalderaFormsV2Contract;
class HooksTest extends TestCase
{


    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Hooks::__construct()
     */
    public function test__construct()
    {
        $container = $this->getContainer();
        $hooks = new Hooks($container);
        $this->assertAttributeInstanceOf(CalderaFormsV2Contract::class, 'container', $hooks );
        $this->assertAttributeInstanceOf(FileFieldHandler::class, 'fileFieldHandler', $hooks );
    }

    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Hooks::getFileFieldHandler()
     */
    public function testGetFileFieldHandler()
    {
        $container = $this->getContainer();
        $hooks = new Hooks($container);
        $this->assertInstanceOf( FileFieldHandler::class, $hooks->getFileFieldHandler() );
    }


}
