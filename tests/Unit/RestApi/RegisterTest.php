<?php

namespace calderawp\calderaforms\Tests\Unit\RestApi;

use calderawp\calderaforms\cf2\RestApi\File\CreateFile;
use calderawp\calderaforms\cf2\RestApi\Process\CreateToken;
use calderawp\calderaforms\cf2\RestApi\Process\Submission;
use calderawp\calderaforms\cf2\RestApi\Queue\RunQueue;
use calderawp\calderaforms\cf2\RestApi\Register;
use calderawp\calderaforms\cf2\RestApi\Token\FormJwt;
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



	/**
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::getEndpoints()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::initEndpoints()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::$endpoints
 		*
	 */
	public function testInitAndGetEndpoints()
	{
		$register = new Register( 'namespace' );
		$register->initEndpoints();
		$this->assertNotEmpty($register->getEndpoints() );
		$this->assertArrayHasKey( Submission::class,$register->getEndpoints() );
		$this->assertArrayHasKey( CreateFile::class,$register->getEndpoints() );
		$this->assertArrayHasKey( RunQueue::class,$register->getEndpoints() );
		$this->assertArrayHasKey( CreateToken::class,$register->getEndpoints() );
	}


	/**
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::setJwt()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::getJwt()
	 */
	public function testGetSetJwt()
	{
		$register = new Register( 'namespace' );
		$register->initEndpoints();
		$jwt = new FormJwt( 'ff', 'https://site.com' );
		$register->setJwt($jwt);
		$this->assertSame($jwt, $register->getJwt() );
	}

	/**
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::setJwt()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::getJwt()
	 */
	public function testSetJwtAddsToRoutes()
	{
		$register = new Register( 'namespace' );
		$register->initEndpoints();
		$jwt = new FormJwt( 'ff', 'https://nom.com' );
		$register->setJwt($jwt);
		$this->assertEquals($jwt, $register->getEndpoints()[ Submission::class ]->getJwt() );
		$this->assertEquals($jwt, $register->getEndpoints()[ CreateToken::class ]->getJwt() );
	}
}
