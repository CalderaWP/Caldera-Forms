<?php


namespace calderawp\calderaforms\cf2\RestApi\Token;


use Firebase\JWT\JWT;

class FormJwt implements UsesJWT
{
	/**
	 * @var string
	 *
	 * @since 1.9.0
	 */
	private $siteUrl;
	/**
	 * @var string
	 *
	 * @since 1.9.0
	 */
	private $secret;

	/**
	 * Create constructor.
	 *
	 * @param string $secret
	 * @param string $siteUrl
	 */
	public function __construct($secret,$siteUrl)
	{
		$this->secret  = $secret;
		$this->siteUrl = $siteUrl;
	}

	/**
	 * Get site secret key
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * Get site url, used for iss
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function getSiteUrl()
	{
		return $this->siteUrl;
	}

	/**
	 * Encode data for a form
	 *
	 * @param string$formId
	 * @param string $uniqueId
	 * @param integer|null $expires
	 *
	 * @return string
	 */
	public function encode( $formId, $uniqueId, $expires = null ){
		$payload = [
			'iss' => $this->getSiteUrl(),
			'cf' => [
				'fI' => $formId,
				'sI' => $uniqueId
			]
		];

		if( is_integer( $expires ) ){
			$payload['exp'] = $expires;
		}

		return JWT::encode($payload,$this->getSecret(), 'HS256');
	}

	public function decode( $encoded ){
		try{
			return JWT::decode( $encoded, $this->getSecret(), ['HS256'] );
		}catch (\Exception $e ){
			return false;
		}
	}

}
