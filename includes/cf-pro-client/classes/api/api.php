<?php


namespace calderawp\calderaforms\pro\api;


/**
 * Class api
 * @package calderawp\calderaforms\pro\api
 */
abstract class api {
	/**
	 * API keys
	 *
	 * @since 0.0.1
	 *
	 * @var keys
	 */
	protected  $keys;

	/**
	 * client constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param keys $keys API keys
	 */
	public function __construct( keys $keys )
	{
		$this->keys = $keys;
	}

	/**
	 * Make remote request
	 *
	 * @since 0.0.1
	 *
	 * @param string $endpoint Endpoint to use
	 * @param array $data Request data to be sent as body or query string for GET.
	 * @param string $method Optional. HTTP request method. Default is "GET"
	 *
	 * @return array|\WP_Error
	 */
	protected function request( $endpoint, $data, $method = 'GET' ){
		$url = untrailingslashit( $this->get_url_root() ) . $endpoint;
		$args = $this->set_request_args( $method );

		if( 'GET' == $method ){
			$url = add_query_arg( $data, $url );
		}else{
			$args[ 'body' ] = wp_json_encode( $data );
		}
		$request = wp_remote_request( $url, $args );
		return $request;

	}

	/**
	 * Create request args for wp_remote_request
	 *
	 * @since 0.2.0
	 *
	 * @param string $method HTTP method
	 *
	 * @return array
	 */
	protected function set_request_args( $method )
	{
		$args = array(
			'headers' => array(
				'X-CS-TOKEN'   => $this->keys->get_token(),
				'X-CS-PUBLIC'  => $this->keys->get_public(),
				'content-type' => 'application/json'

			),
			'method'  => $method,
			'timeout' => 30
		);

		return $args;
	}



	/**
	 * Get root URL for API
	 *
	 * @since 0.6.0
	 *
	 * @return string
	 */
	abstract protected function get_url_root();

}