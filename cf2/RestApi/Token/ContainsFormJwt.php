<?php


namespace calderawp\calderaforms\cf2\RestApi\Token;


trait ContainsFormJwt
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
		$this->setJwtProp($jwt);
		return $this;
	}

	/**
	 * Sets prop for jwt
	 *
	 * Hack to make work in Register class
	 *
	 * @since 1.9.0
	 *
	 * @param FormTokenContract $jwt
	 */
	protected function setJwtProp(FormTokenContract $jwt)
	{
		$this->jwt = $jwt;
	}
}
