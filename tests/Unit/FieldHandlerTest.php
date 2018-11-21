<?php

namespace calderawp\calderaforms\Tests\Unit;

use calderawp\calderaforms\cf2\CalderaFormsV2;
use calderawp\calderaforms\cf2\Fields\Handlers\FieldHandler;
use calderawp\calderaforms\Tests\Util\Mocks\MockFieldHandler;

class FieldHandlerTest extends TestCase
{

    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\Fields\Handlers\FieldHandler::__construct()
     */
    public function test__construct()
    {
        $container = $this->getContainer();
        $handler = new MockFieldHandler($container);
        $this->assertAttributeInstanceOf(CalderaFormsV2::class, 'container', $handler );
    }
}
