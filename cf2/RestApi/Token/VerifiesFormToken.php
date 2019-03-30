<?php


namespace calderawp\calderaforms\cf2\RestApi\Token;

trait VerifiesFormToken
{

	/**
	 * JWT
	 *
	 * @since 1.9.0
	 *
	 * @var FormTokenContract
	 */
	private $jwt;

	/**
	 * Get the JWT encoder/decoder
	 *
	 * @since 1.9.0
	 *
	 * @return FormTokenContract
	 */
	public function getJwt()
	{
		return $this->jwt;
	}

	/**
	 * Set the JWT encoder/decoder
	 *
	 * @since 1.9.0
	 *
	 * @param FormTokenContract $jwt
	 *
	 * @return VerifiesFormToken
	 */
	public function setJwt(FormTokenContract $jwt)
	{
		$this->jwt = $jwt;
		return $this;
	}

	/**
	 * Permissions callback
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_REST_Request $request Request object
	 *
	 * @return bool
	 */
	public function permissionsCallback(\WP_REST_Request $request ){
		$token = $this->getTokenFromRequest($request);
		if( empty($token) ){
			return false;
		}

		return $this
			->getJwt()
			->decodeAndCheckPublic(
				$token,
				$this->getFormIdFromRequest($request),
				$this->getSessionIdFromRequest($request)
			);
	}

	/**
	 * Get the token from incoming request
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_REST_Request
	 *
	 * @return mixed
	 */
	abstract protected function getTokenFromRequest(\WP_REST_Request $request );

	/**
	 * Get form ID from request
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_REST_Request
	 *
	 * @return string
	 */
	abstract protected function getFormIdFromRequest(\WP_REST_Request $request);

	/**
	 * Get session ID from request
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_REST_Request
	 *
	 * @return string
	 */
	abstract protected function getSessionIdFromRequest(\WP_REST_Request $request);

}
