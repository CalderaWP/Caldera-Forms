<?php


class ServiceContainerTest extends TestCase
{

    /**
     * Test provider registration
     *
     * @covers Container::bind()
     * @covers Container::make()
     */
    public function testRegisterProvider()
    {
        $ref = 'roy';
        $container = new \calderawp\CalderaContainers\Service\Container();
        $container->bind( $ref, function (){
            return new \calderawp\CalderaContainers\Tests\Mocks\Something();
        });

        $this->assertSame( \calderawp\CalderaContainers\Tests\Mocks\Something::class, get_class( $container->make($ref )) );
    }

    /**
     *
     * @covers Container::bind()
     * @covers Container::make()
     */
    public function testRegisterTwoProviders()
    {
        $classRef1 = \calderawp\CalderaContainers\Tests\Mocks\Something::class;
        $container = new \calderawp\CalderaContainers\Service\Container();
        $container->bind( $classRef1, function (){
            return new \calderawp\CalderaContainers\Tests\Mocks\Something();
        });

        $classRef2 = \calderawp\CalderaContainers\Tests\Mocks\SomethingElse::class;
        $container->bind( $classRef2, function (){
            return new \calderawp\CalderaContainers\Tests\Mocks\SomethingElse();
        });

        $this->assertSame( $classRef1, get_class( $container->make($classRef1 )) );
        $this->assertSame( $classRef2, get_class( $container->make($classRef2 )) );
    }

    /**
     * Test that each object returned by bind, that is not set to be a singleton
     *
     * @covers Container::bind()
     * @covers Container::make()
     */
    public function testBindNotSingleton()
    {

        $classRef1 = \calderawp\CalderaContainers\Tests\Mocks\Something::class;
        $container = new \calderawp\CalderaContainers\Service\Container();
        $container->bind( $classRef1, function (){
            $entity = new \calderawp\CalderaContainers\Tests\Mocks\Something();
            $entity->prop = rand();
            return $entity;
        });

        $this->assertNotEquals( $container->make($classRef1),$container->make($classRef1));
        $this->assertNotEquals( $container->make($classRef1)->prop,$container->make($classRef1)->prop);

    }

    /**
     * Test that objects bound as singletons always return the same instance
     *
     * @covers Container::singleton()
     * @covers Container::bind()
     * @covers Container::make()
     */
    public function testSingleton()
    {
        $container = new \calderawp\CalderaContainers\Service\Container();

        $classRef =\calderawp\CalderaContainers\Tests\Mocks\Something::class;
        $container->singleton( $classRef, new \calderawp\CalderaContainers\Tests\Mocks\Something());

        $this->assertSame( $container->make($classRef), $container->make($classRef));

    }
}