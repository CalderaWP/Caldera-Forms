<?php


namespace calderawp\calderaforms\pro\api\local;

use calderawp\calderaforms\pro\container;
use calderawp\calderaforms\pro\exceptions\Exception;


/**
 * Class files
 * @package calderawp\calderaforms\pro\api\local
 */
class files implements \Caldera_Forms_API_Route {

	/**
	 * @inheritdoc
	 */
	public function add_routes($namespace)
	{
		register_rest_route($namespace, '/pro/file', [
				'methods' => 'GET',
				'permission_callback' => [ $this, 'key_check' ],
				'callback' => [ $this, 'get_file' ],
				'args' => [
					'file' => [
						'type' => 'string',
						'required' => 'true',
					],
				],
			]
		);

	}

	/**
	 * Check API keys
	 *
	 * @since 1.5.8
	 * @since 0.9.0 cf-pro
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function key_check(\WP_REST_Request $request)
	{
		$public = !empty($request[ 'public' ]) ? $request[ 'public' ] : !is_null($request->get_header('X-CS-PUBLIC')) ? $request->get_header('X-CS-PUBLIC') : null;
		$token = !empty($request[ 'token' ]) ? $request[ 'token' ] : !is_null($request->get_header('X-CS-TOKEN')) ? $request->get_header('X-CS-TOKEN') : null;
		return caldera_forms_pro_compare_to_saved_keys($public, $token);
	}

	/**
	 * Get file and infos
	 *
	 * @since 1.5.8
	 * @since 0.9.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_file(\WP_REST_Request $request)
	{
		$file = $this->get_file_from_request($request);
		if ( $file && file_exists($file) ) {
			$hash  = !empty($request[ 'hash' ]) ? $request[ 'hash' ] : '';
			if( ! $this->verify_file($file,$hash ) ){
				return new \WP_Error( 412, 'Hash is not valid', 'caldera-forms' );
			}
			return rest_ensure_response([
				'name' => basename($file),
				'mime' => mime_content_type($file),
				'contents' => base64_encode(file_get_contents($file)),
			]);
		}
		container::get_instance()->get_logger()->send('File not found by CF Pro for attachment', [
			'file' => $request[ 'file' ],
		]);

		return new \WP_Error('404', __('File not found', 'caldera-forms'));
	}

	/**
	 * Verify file hash
	 *
	 * @since 1.8.0
	 *
	 * @param string $file File path
	 * @param string $hash File hash
	 *
	 * @return bool
	 */
	public function verify_file( $file, $hash )
	{

		if ( $file && file_exists($file) ) {
			$secret  = container::get_instance()->get_settings()->get_api_keys()->get_secret();
			if( $secret && hash_equals( hash_hmac( 'sha256', $file, $secret  ), $hash ) ){
				return true;
			}
			return null;
		}
		return false;

	}

	/**
	 * Get the file from request object
	 *
	 * @since 1.8.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return string
	 */
	protected function get_file_from_request(\WP_REST_Request $request)
	{
		return !empty($request[ 'file' ]) ? $request[ 'file' ] : '';
	}

}
