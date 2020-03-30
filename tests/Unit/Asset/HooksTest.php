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

    /**
     * @covers \calderawp\calderaforms\cf2\Asset\Hooks::registerAssets()
     */
    public function testRegisterAssetsNoHandles()
    {
        $hooks = new Hooks([], $this->container);
        $this->assertEquals($hooks, $hooks->registerAssets());
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Asset\Hooks::subscribe()
     */
    public function testSubscribe()
    {
        $hooks = new Hooks(['form-builder'], $this->container);
        $hooks->subscribe();
        $this->assertTrue(has_action('admin_enqueue_scripts'), [$hooks, 'enqueueAdminAssets']);
        $this->assertTrue(has_action('wp_register_scripts'), [$hooks, 'registerAssets']);
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Asset\Hooks::getHandler()
     */
    public function testGetHandler()
    {
        $hooks = new Hooks(['form-builder'], $this->container);
        $this->assertInstanceOf(Register::class, $hooks->getHandler('form-builder'));
        $this->assertStringEndsWith(
            '/clients/form-builder/build/index.min.asset.json',
            $hooks->getHandler('form-builder')->getAssetFilePath()
        );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Asset\Hooks::subscribe()
     * @covers \calderawp\calderaforms\cf2\Asset\Hooks::maybeUseManifest()
     */
    public function testUseManifest()
    {
        $hooks = new Hooks(['form-builder', 'arms'], $this->container,
            [
                'arms.js' => 'https://website.wordpress/something/arms.js',
                'arms.json' => dirname(__FILE__ ) .'/assets.json'
            ]);

        $hooks->subscribe();
        $this->assertInstanceOf(Register::class, $hooks->getHandler('arms'));
        //Does not change defaults with no override
        $this->assertStringEndsWith(
            '/clients/form-builder/build/index.min.asset.json',
            $hooks->getHandler('form-builder')->getAssetFilePath()
        );
        $this->assertStringEndsWith(
            'build/index.min.js',
            $hooks
                ->getHandler('form-builder')
                ->getScriptUrl()
        );

        //Does  change defaults when has override
        $this->assertEquals(
            dirname(__FILE__ ) .'/assets.json',
            $hooks->getHandler('arms')->getAssetFilePath()
        );

        $this->assertEquals(
            'https://website.wordpress/something/arms.js',
            $hooks
                ->getHandler('arms')
                ->getScriptUrl()
        );

    }
}
