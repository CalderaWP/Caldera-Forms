<?php

namespace calderawp\calderaforms\Tests\Unit\RestApi;

use calderawp\calderaforms\cf2\RestApi\Register;
use calderawp\calderaforms\Tests\Unit\TestCase;

class RegisterTest extends TestCase
{


    /**
     *
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\RestApi\Register::$namespace
     * @covers \calderawp\calderaforms\cf2\RestApi\Register::getNamespace()
     *
     * @group api3
     */
    public function testGetNamespace()
    {
        $register = new Register('cf22' );
        $this->assertEquals( 'cf22', $register->getNamespace() );
    }

    /**
     *
     * @since 1.8.0
     *
     * @covers \calderawp\calderaforms\cf2\RestApi\Register::$namespace
     * @covers \calderawp\calderaforms\cf2\RestApi\Register::__construct()
     */
    public function test__construct()
    {
        $register = new Register('cf22' );
        $this->assertAttributeEquals('cf22', 'namespace', $register );

    }
}
