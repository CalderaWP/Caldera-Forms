<?php


namespace calderawp\calderaforms\Tests\Unit;

use calderawp\calderaforms\cf2\Exception;
class ExceptionTest extends TestCase
{

    /**
     * @covers \alderawp\calderaforms\cf2\Exception::toWpError()
     */
    public function testToWpError()
    {

        $wpError = \Mockery::mock('WP_Error');
        $this->assertInstanceOf(
            'WP_Error',
            (new Exception(500))
                ->toWpError([])
        );


    }

    /**
     * @covers \calderawp\calderaforms\cf2\Exception::formOtherException()
     */
    public function testFormOtherException()
    {
        $code = 500;
        $message = 'fail';
        $data = [1 => 2];
        $original = new \Exception($message,$code);
        $e = Exception::formOtherException($original);
        $this->assertEquals($code,$e->getCode());
        $this->assertEquals($message,$e->getMessage());
    }

    /**
     * @since 1.8.0
     *
     * @group Exception
     * @group cf2
     *
     * @covers \calderawp\calderaforms\cf2\Exception::fromWpError()
     */
    public function testFromWpError()
    {
        $code = 500;
        $message = 'fail';
        $wpError = \Mockery::mock('WP_Error');
        $wpError->shouldReceive( 'get_error_code' )
            ->andReturn( $code );
        $wpError->shouldReceive( 'get_error_message' )
            ->andReturn( $message );

        $e = Exception::fromWpError($wpError);
        $this->assertEquals($code,$e->getCode());
        $this->assertEquals($message,$e->getMessage());

    }



}