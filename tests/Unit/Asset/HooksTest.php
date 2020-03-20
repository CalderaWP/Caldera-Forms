<?php

namespace calderawp\calderaforms\Tests\Unit\Asset;

use calderawp\calderaforms\cf2\Asset\Hooks;
use calderawp\calderaforms\cf2\Asset\Register;
use calderawp\calderaforms\Tests\Unit\TestCase;

class HooksTest extends TestCase
{

    /**
     * @var \calderawp\calderaforms\cf2\CalderaFormsV2
     */
    protected $container;

    /** @inheritDoc */
    public function setUp()
    {
        $this->container = $this->getContainer();
        $this->container->setCoreDir(dirname(__DIR__, 3));
        parent::setUp();
    }

    public function testRegisterAssets()
    {
        $hooks = new Hooks(['form-builder'], $this->container);
        \Brain\Monkey\Functions\expect('wp_register_script')->once();
        \Brain\Monkey\Functions\expect('wp_localize_script')->once();
        $hooks->registerAssets();

    }

    public function testSubscribe()
    {
        $hooks = new Hooks(['form-builder'], $this->container);
        $hooks->subscribe();
        $this->assertTrue(has_action('admin_enqueue_scripts'), [$hooks, 'enqueueAdminAssets']);
        $this->assertTrue(has_action('wp_register_scripts'), [$hooks, 'registerAssets']);
    }


    public function testGetHandler()
    {
        $hooks = new Hooks(['form-builder'], $this->container);
        $this->assertInstanceOf(Register::class, $hooks->getHandler('form-builder'));
        $this->assertStringEndsWith(
            '/clients/form-builder/build/index.min.asset.json',
            $hooks->getHandler('form-builder')->getAssetFilePath()
        );
    }
}
