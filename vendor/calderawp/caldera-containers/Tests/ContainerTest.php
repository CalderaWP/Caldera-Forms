<?php


class ContainerTest extends TestCase
{

    /**
     *
     * @covers  \calderawp\CalderaContainers\Container::get()
     * @covers  \calderawp\CalderaContainers\Container::set()
     * @covers  \calderawp\CalderaContainers\Container::offsetGet()
     * @covers  \calderawp\CalderaContainers\Container::offsetSet()
     */
    public function testSet()
    {
        $container = new \calderawp\CalderaContainers\Tests\Mocks\Container();
        $container->set('hi', 'roy' );
        $this->assertEquals( $container[ 'hi'], $container->get('hi' ) );

        $container = new \calderawp\CalderaContainers\Tests\Mocks\Container();
        $container[ 'x' ] = 1;
        $this->assertEquals( 1, $container[ 'x' ] );
        $this->assertEquals( $container->get('x'), $container[ 'x' ] );


        $container = new \calderawp\CalderaContainers\Tests\Mocks\Container();
        $y = new stdClass();
        $y->x = 1;
        $container->set( 'y', $y );
        $this->assertSame( $y, $container->get( 'y' ) );



    }

    /**
     * @covers  \calderawp\CalderaContainers\Container::has()
     * @covers  \calderawp\CalderaContainers\Container::offsetExists()
     */
    public function testHas()
    {
        $container = new \calderawp\CalderaContainers\Tests\Mocks\Container();
        $container[ 'x' ] = 1;
        $this->assertTrue( $container->has('x' ) );
        $this->assertFalse( $container->has('y' ) );
    }

    /**
     * @covers  \calderawp\CalderaContainers\Container::has()
     * @covers  \calderawp\CalderaContainers\Container::offsetUnset()
     */
    public function testUnset()
    {
        $container = new \calderawp\CalderaContainers\Tests\Mocks\Container();
        $container[ 'x' ] = 1;
        unset( $container['x'] );
        $this->assertFalse( $container->has('x' ) );
    }
}