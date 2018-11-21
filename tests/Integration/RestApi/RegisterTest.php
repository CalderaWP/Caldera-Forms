<?php


namespace calderawp\calderaforms\Tests\Integration\RestApi;


class RegisterTest extends RestApiTestCase
{
    /**
     *
     * @covers \calderawp\calderaforms\cf2\RestApi\Register::initEndpoints();
     *
     * @group api3
     */
    public function testV3Url()
    {
        global $wp_rest_server;
        $routes = $wp_rest_server->get_routes();
        $this->assertArrayHasKey('/cf-api/v3/file' , $routes );
    }
}