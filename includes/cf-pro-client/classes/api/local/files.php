<?php


namespace calderawp\calderaforms\pro\api\local;
use calderawp\calderaforms\pro\container;


/**
 * Class files
 * @package calderawp\calderaforms\pro\api\local
 */
class files implements \Caldera_Forms_API_Route {
	/**
	 * @inheritdoc
	 */
	public function add_routes( $namespace ) {
		register_rest_route( $namespace, '/pro/file', [
				'methods'     => 'GET',
				'permissions' => [ $this, 'key_check' ],
				'callback'    => [ $this, 'get_file' ],
				'args'        => [
					'file' => [
						'type'     => 'string',
						'required' => 'true'
					]
				]
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
	public function key_check( \WP_REST_Request $request ){
		$public = ! empty( $request[ 'public' ] ) ? $request[ 'public' ] : ! is_null( $request->get_header( 'X-CS-PUBLIC' ) ) ? $request->get_header( 'X-CS-PUBLIC' ) : null;
		$token = ! empty( $request[ 'token' ] ) ? $request[ 'token' ] : ! is_null( $request->get_header( 'X-CS-TOKEN' ) ) ? $request->get_header( 'X-CS-TOKEN' ) : null;
		return caldera_forms_pro_compare_to_saved_keys( $public, $token );
	}

	/**
	 * Get file and infos
     *
     * @since 1.5.8
     * @since 0.9.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error
	 */
	public function get_file( \WP_REST_Request $request ){
		$file = $request[ 'file' ];
		if (file_exists($file)) {
			return rest_ensure_response([
				'name' => basename( $file ),
				'mime' => mime_content_type( $file ),
				'contents' => base64_encode( file_get_contents( $file ) )
			]);
		}
		container::get_instance()->get_logger()->send( 'File not found by CF Pro for attachment', [
			'file' => $request[ 'file' ]
		]);

		return new \WP_Error( '404', __( 'File not found', 'caldera-forms' ) );
	}

}