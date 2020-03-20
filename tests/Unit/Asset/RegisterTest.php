<?php

namespace calderawp\calderaforms\Tests\Unit\Asset;

use calderawp\calderaforms\cf2\Asset\Register;
use calderawp\calderaforms\Tests\Unit\TestCase;

class RegisterTest extends TestCase
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
     * @covers \calderawp\calderaforms\cf2\Asset\Register::register()
     * @covers \calderawp\calderaforms\cf2\Asset\Register::isRegistered()
     */
    public function testRegister()
    {
        $register = $this->getRegister();
        \Brain\Monkey\Functions\expect('wp_register_script')->once();
        \Brain\Monkey\Functions\expect('wp_localize_script')->once();
        $this->assertTrue($register
            ->register()
            ->isRegistered()
        );

    }

    /**
     * @covers \calderawp\calderaforms\cf2\Asset\Register::enqueue()
     * @covers \calderawp\calderaforms\cf2\Asset\Register::register()
     * @covers \calderawp\calderaforms\cf2\Asset\Register::isRegistered()
     */
    public function testRegisterAndEnqueue()
    {
        $register = $this->getRegister();

        \Brain\Monkey\Functions\expect('wp_register_script')->once();
        \Brain\Monkey\Functions\expect('wp_localize_script')->once();

        $this->assertTrue($register
            ->register()
            ->isRegistered()
        );
        \Brain\Monkey\Functions\expect('wp_enqueue_script')->once();

        $this->assertEquals($register, $register->enqueue());
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Asset\Register::setAssetsFilePath()
     * @covers \calderawp\calderaforms\cf2\Asset\Register::getAssetFilePath()
     */
    public function testSetAssetFilePath()
    {
        $register = $this->getRegister();
        $path = '/solids/liquids';
        $this->assertSame(
            $path,
            $register
                ->setAssetsFilePath($path)
                ->getAssetFilePath()
        );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Asset\Register::setScriptUrl()
     * @covers \calderawp\calderaforms\cf2\Asset\Register::getScriptUrl()
     */
    public function testGetScriptUrl()
    {
        $register = $this->getRegister();
        $this->assertStringEndsWith('/clients/form-builder/build/index.min.js', $register->getScriptUrl());
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Asset\Register::setScriptUrl()
     * @covers \calderawp\calderaforms\cf2\Asset\Register::getScriptUrl()
     */
    public function testSetScriptUrl()
    {
        $register = $this->getRegister();
        $url = 'https://rocks.solids';
        $this->assertSame(
            $url,
            $register
                ->setScriptUrl($url)
                ->getScriptUrl()
        );
    }

    /**
     * @covers \calderawp\calderaforms\cf2\Asset\Register::setAssetsFilePath()
     * @covers \calderawp\calderaforms\cf2\Asset\Register::getAssetFilePath()
     */
    public function testGetAssetsFilePath()
    {
        $register = $this->getRegister();
        $this->assertStringEndsWith('/clients/form-builder/build/index.min.asset.json', $register->getAssetFilePath());
    }


    /**
     * @return Register
     */
    protected function getRegister()
    {
        $register = new Register('form-builder', $this->container->getCoreUrl(), $this->container->getCoreDir());
        return $register;
    }
}
