<?php

namespace calderawp\calderaforms\Tests\Integration\RestApi;

use calderawp\calderaforms\cf2\Jobs\DeleteTransientJob;
use calderawp\calderaforms\cf2\RestApi\Queue\RunQueue;
use calderawp\calderaforms\pro\container;

class RunQueueTest extends RestApiTestCase
{

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::initEndpoints()
	 * @covers \calderawp\calderaforms\cf2\RestApi\RunQueue::getUri()
	 */
	public function testRouteCanBeRequest()
	{
		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );
		$request = new \WP_REST_Request('GET', '/cf-api/v3');
		$response = rest_get_server()->dispatch($request);
		$this->assertTrue(
			array_key_exists(  $uri, $response->get_data()[ 'routes'] )
		);
		$this->assertTrue(
			in_array( 'POST', $response->get_data()[ 'routes'][ $uri ]['methods'] )
		);

	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\RunQueue::runQueue()
	 */
	public function testRunQueue()
	{
		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$id1 = uniqid('a');
		$id2 = uniqid('b');
		\Caldera_Forms_Transient::set_transient($id1,rand());
		\Caldera_Forms_Transient::set_transient($id2,rand());

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_param( 'jobs', 5 );
		$endpoint->runQueue($request);
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testCheckKeysWithHeaders()
	{

		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$token = container::get_instance()->get_settings()->get_api_keys()->get_token();

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_header( 'X-CS-PUBLIC','pub' );
		$request->set_header( 'X-CS-TOKEN',$token );
		$request->set_param( 'jobs', 5 );
		$this->assertTrue( $endpoint->checkKeys($request ) );

	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testGetPublicWithParam(){
		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );
		$request = new \WP_REST_Request('POST', $uri);
		$request->set_param( 'public','pub' );
		$this->assertEquals( 'pub', $endpoint->getPublic($request));
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testGetPublicWithHeader(){
		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );
		$request = new \WP_REST_Request('POST', $uri);
		$request->set_header( 'X-CS-PUBLIC','pub' );
		$this->assertEquals( 'pub', $endpoint->getPublic($request));
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 */
	public function testGetTokenWithParam(){
		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );
		$request = new \WP_REST_Request('POST', $uri);
		$request->set_param( 'token','ttt' );
		$this->assertEquals( 'ttt', $endpoint->getToken($request));
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 */
	public function testGetTokenWithHeader(){
		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );
		$request = new \WP_REST_Request('POST', $uri);
		$request->set_header( 'X-CS-TOKEN','ttt' );
		$this->assertEquals( 'ttt', $endpoint->getToken($request));
	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testCheckKeysWithRequestParams()
	{

		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$token = container::get_instance()->get_settings()->get_api_keys()->get_token();

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_param( 'public','pub' );
		$request->set_param( 'token',$token );
		$request->set_param( 'jobs', 5 );
		$this->assertTrue( $endpoint->checkKeys($request ) );

	}
	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testCheckKeysWithInvalidHeaders()
	{

		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_header( 'X-CS-PUBLIC','lll' );
		$request->set_header( 'X-CS-TOKEN','l' );
		$request->set_param( 'jobs', 5 );
		$this->assertFalse( $endpoint->checkKeys($request ) );

	}
	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testcheckKeysWithInvalidRequestParams()
	{

		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$token = container::get_instance()->get_settings()->get_api_keys()->get_token();

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_param( 'public','pubzz' );
		$request->set_param( 'token',uniqid('xc') );
		$request->set_param( 'jobs', 5 );
		$this->assertFalse( $endpoint->checkKeys($request ) );

	}
	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testcheckKeysWithValidTokenNoPublicHeaders()
	{

		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$token = container::get_instance()->get_settings()->get_api_keys()->get_token();

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_header( 'X-CS-TOKEN',$token );
		$request->set_param( 'jobs', 5 );
		$this->assertFalse( $endpoint->checkKeys($request ) );

	}
	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testCheckKeysWithValidTokenNoPublic()
	{

		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$token = container::get_instance()->get_settings()->get_api_keys()->get_token();

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_param( 'token',$token );
		$request->set_param( 'jobs', 5 );
		$this->assertFalse( $endpoint->checkKeys($request ) );

	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\RunQueue::runQueue()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testFullDispatchRequestWithInvalidAuth(){
		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_param( 'token','fff' );
		$request->set_param( 'jobs', 5 );

		$response = rest_get_server()->dispatch($request);
		$this->assertSame( 401, $response->get_status() );

	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\RunQueue::runQueue()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */	public function testDispatchWithNoJobs200StatusCode(){
		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$token = container::get_instance()->get_settings()->get_api_keys()->get_token();

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_header( 'X-CS-PUBLIC','pub' );
		$request->set_header( 'X-CS-TOKEN',$token );
		$request->set_param( 'jobs', 5 );

		$response = rest_get_server()->dispatch($request);
		$this->assertSame( 200, $response->get_status() );

	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\RunQueue::runQueue()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */	public function testDispatchWithJobs201StatusCode(){
		$id1 = uniqid('r2' );
		$id2 = uniqid('d2' );
		\Caldera_Forms_Transient::set_transient($id1,'1' );
		\Caldera_Forms_Transient::set_transient($id2,'1a' );
		/** @var \calderawp\calderaforms\cf2\Jobs\Scheduler $scheduler */
		$scheduler = caldera_forms_get_v2_container()->getService(\calderawp\calderaforms\cf2\Services\QueueSchedulerService::class);
		$scheduler->schedule(new DeleteTransientJob($id1 ));
		$scheduler->schedule(new DeleteTransientJob($id2 ));


		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$token = container::get_instance()->get_settings()->get_api_keys()->get_token();

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_header( 'X-CS-PUBLIC','pub' );
		$request->set_header( 'X-CS-TOKEN',$token );

		$request->set_param( 'jobs', 5 );

		$response = rest_get_server()->dispatch($request);
		$this->assertSame( 201, $response->get_status() );

	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\RunQueue::runQueue()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::checkKeys()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getToken()
	 * @covers \calderawp\calderaforms\cf2\RestApi\AuthorizesRestApiRequestWithCfProKeys::getPublic()
	 */
	public function testResponse(){
		$id1 = uniqid('r2' );
		$id2 = uniqid('d2' );
		\Caldera_Forms_Transient::set_transient($id1,'1' );
		\Caldera_Forms_Transient::set_transient($id2,'1a' );
		/** @var \calderawp\calderaforms\cf2\Jobs\Scheduler $scheduler */
		$scheduler = caldera_forms_get_v2_container()->getService(\calderawp\calderaforms\cf2\Services\QueueSchedulerService::class);
		$scheduler->schedule(new DeleteTransientJob($id1 ));
		$scheduler->schedule(new DeleteTransientJob($id2 ));


		container::get_instance()->get_settings()->set_api_public('pub' );
		container::get_instance()->get_settings()->set_api_secret('secret' );

		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$token = container::get_instance()->get_settings()->get_api_keys()->get_token();

		$request = new \WP_REST_Request('POST', $uri);
		$request->set_header( 'X-CS-PUBLIC','pub' );
		$request->set_header( 'X-CS-TOKEN',$token );

		$request->set_param( 'jobs', 5 );

		$response = rest_get_server()->dispatch($request);
		$data = $response->get_data();
		$this->assertArrayHasKey( 'totalJobs', $data );
		$this->assertSame( 2, $data['totalJobs'] );

	}
}
