<?php


namespace calderawp\calderaforms\cf2\RestApi\Token;


interface UsesFormJwtContract
{
	/**
	 * Get the JWT encoder/decoder
	 *
	 * @since 1.9.0
	 *
	 * @return FormTokenContract
	 */
	public function getJwt();

	/**
	 * Set the JWT encoder/decoder
	 *
	 * @since 1.9.0
	 *
	 * @param FormTokenContract $jwt
	 *
	 * @return VerifiesFormToken
	 */
	public function setJwt(FormTokenContract $jwt);
}
