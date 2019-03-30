<?php


namespace calderawp\calderaforms\cf2\RestApi\Process;

use calderawp\calderaforms\cf2\RestApi\Endpoint;
use calderawp\calderaforms\cf2\RestApi\Token\VerifiesFormToken;
use calderawp\calderaforms\cf2\RestApi\Token\UsesFormJwtContract;

class CreateToken extends Submission implements UsesFormJwtContract
{
	use VerifiesFormToken;

	/**
	 * @inheritDoc
	 */
	protected function getUri()
	{
		return parent::getUri() . '/token';
	}

	/**
	 * @inheritDoc
	 */
	protected function getArgs()
	{
		return [

			'methods' => 'PUT',
			'callback' => [$this, 'createItem'],
			'args' => [
				'formId' => [
					'type' => 'string',
					'description' => __('ID for form field belongs to', 'caldera-forms'),
					'required' => true,
				],
			],
		];
	}

	/**
	 * Create session token
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function createItem(\WP_REST_Request $request)
	{
		$formId = $this->getFormIdFromRequest($request);
		$sessionId = hash_hmac( 'sha256', $formId, $this->getJwt()->getSecret()  );

		$response = rest_ensure_response([
			Submission::SESSION_ID_FIELD => $sessionId,
			Submission::VERIFY_FIELD => $this
				->getJwt()
				->encode(
					$formId,
					$sessionId
				),

		]);
		$response->set_status(201);
		return $response;
	}


}
