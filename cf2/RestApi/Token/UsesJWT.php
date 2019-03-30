<?php


namespace calderawp\calderaforms\cf2\RestApi\Token;


interface UsesJWT
{
	/**
	 * Get site secret key
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function getSecret();

	/**
	 * Get site url, used for iss
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function getSiteUrl();
}
