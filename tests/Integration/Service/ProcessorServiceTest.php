<?php

namespace calderawp\calderaforms\Tests\Integration\Service;

use calderawp\calderaforms\cf2\Factories\ProcessorFactory;
use calderawp\calderaforms\cf2\Services\ProcessorService;
use calderawp\calderaforms\Tests\Integration\TestCase;

class ProcessorServiceTest extends TestCase
{
    /**
     * @since 1.8.10
     *
     * @covers \calderawp\calderaforms\cf2\Services\ProcessorService::isSingleton()
     */
    public function testIsSingleton()
    {
        $service = new ProcessorService();
        $this->assertTrue($service->isSingleton());
    }

    /**
     * @since 1.8.10
     *
     * @covers \calderawp\calderaforms\cf2\Services\ProcessorService::getIdentifier()
     */
    public function testGetIdentifier()
    {
        $this->assertTrue(is_string(
            (new ProcessorService())->getIdentifier()
        ));
    }

    /**
     * @since 1.8.10
     *
     * @covers \calderawp\calderaforms\cf2\Services\ProcessorService::register()
     */
    public function testRegister()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(ProcessorFactory::class, (new ProcessorService())->register($container));
    }
}
