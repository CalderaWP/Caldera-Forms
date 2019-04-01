<?php


namespace calderawp\calderaforms\cf2\RestApi\Token;


use calderawp\calderaforms\cf2\Exception;
use Firebase\JWT\JWT;

/**
 * Class FormJwt
 *
 * This is used to verify a session ID, bound to a form for when WordPress nonces can not be used.
 *
 * Do NOT use for authentication.
 * Do NOT transmit sensitive data in token.
 */
class FormJwt implements FormTokenContract
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
	 * Create FormJWT instance
	 *
	 * @since  1.9.0
	 *
	 * @param string $secret JWT Secret key
	 * @param string $siteUrl Site URL - used for iss
	 */
	public function __construct($secret,$siteUrl)
	{
		$this->secret  = $secret;
		$this->siteUrl = $siteUrl;
	}

	/**
	 * @inheritdoc
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * @inheritdoc
	 */
	public function getSiteUrl()
	{
		return $this->siteUrl;
	}

	/**
	 * @inheritdoc
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
			//$payload['exp'] = $expires;
		}

		return JWT::encode($payload,$this->getSecret(), 'HS256');
	}

	/**
	 * @inheritdoc
	 */
	public function decode( $encoded ){
		try{
			return JWT::decode( $encoded, $this->getSecret(), ['HS256'] );
		}catch (\Exception $e ){
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function decodeAndCheckPublic($encoded, $formId, $sessionPublicKey)
	{
		$decoded = $this->decode($encoded);
		if( ! $decoded ){
			return false;
		}

		if( ! isset( $decoded->cf ) || ! isset( $decoded->cf->fI ) || ! isset( $decoded->cf->sI ) ){
			return false;
		}

		if( ! hash_equals( $formId, $decoded->cf->fI ) ){
			return false;
		}

		if( ! hash_equals( $sessionPublicKey, $decoded->cf->sI ) ){
			return false;
		}

		return true;
	}

}
