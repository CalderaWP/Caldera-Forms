<?php

namespace calderawp\calderaforms\Tests\Unit;

use calderawp\calderaforms\cf2\CalderaFormsV2;
use calderawp\calderaforms\cf2\Hooks;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;

class CalderaFormsV2Test extends TestCase
{
    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::getTransientsApi();
     */
    public function testGetTransientsApi()
    {
        $containerMock = $this->getContainer();
        $this->assertInstanceOf(Cf1TransientsApi::class, $containerMock->getTransientsApi() );
        $containerReal = new CalderaFormsV2();
        $this->assertInstanceOf(Cf1TransientsApi::class, $containerReal->getTransientsApi() );



    }

    /**
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\CalderaFormsV2::getHooks();
     */
    public function testGetHooks()
    {
        $containerMock = $this->getContainer();
        $this->assertInstanceOf(Hooks::class, $containerMock->getHooks() );
        $containerReal = new CalderaFormsV2();
        $this->assertInstanceOf(Hooks::class, $containerReal->getHooks() );
    }
}
