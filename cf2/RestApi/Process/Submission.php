<?php


namespace calderawp\calderaforms\cf2\RestApi\Process;


use calderawp\calderaforms\cf2\RestApi\Endpoint;

class Submission extends Endpoint
{

	const URI = 'process/submission';

	const VERIFY_FIELD = '_cf_verify';

	/**
	 * @return string
	 */
	protected function getUri()
	{
		return self::URI;
	}

	/** @inheritdoc */
	protected function getArgs()
	{
		return [

			'methods' => 'POST',
			'callback' => [$this, 'createItem'],
			'permission_callback' => [$this, 'permissionsCallback' ],
			'args' => [
				self::VERIFY_FIELD => [
					'type' => 'string',
					'description' => __('Verification token (nonce) for form', 'caldera-forms'),
					'required' => true,
				],
				'formId' => [
					'type' => 'string',
					'description' => __('ID for form field belongs to', 'caldera-forms'),
					'required' => true,
				],
				'entryData' => [
					'type' => 'array',
					'description' => __('Entry Data', 'caldera-forms'),

				]
			]
		];
	}

	/**
	 * Permissions check for file field uploads
	 *
	 * @since 1.8.0
	 *
	 * @param \WP_REST_Request $request Request object
	 *
	 * @return bool
	 */
	public function permissionsCallback(\WP_REST_Request $request ){
		return ! empty($request->get_param( 'verify') );
		return \Caldera_Forms_Render_Nonce::verify_nonce(
			$request->get_param( 'verify'),
			$request->get_param( 'formId' )
		);
	}



	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 * @throws \Exception
	 */
	public function createItem(\WP_REST_Request $request)
	{


		$entryId = 0;
		if( is_numeric($entryId ) ){
			$response = rest_ensure_response( ['entryId' => $entryId ] );
			$response->set_status(201);
		}else{
			$response = rest_ensure_response( new \WP_Error() );
		}
		return $response;
	}
}
