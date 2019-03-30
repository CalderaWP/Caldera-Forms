<?php


namespace calderawp\calderaforms\cf2\RestApi\Token;

/**
 * Interface FormTokenContract
 *
 * Contract for classes that use form JWT tokens
 */
interface FormTokenContract
{


	/**
	 * Encode data for a form
	 *
	 * @since 1.9.0
	 *
	 * @param string $formId Form ID to use with
	 * @param string $uniqueId Unique session ID - for use with CF1 should be transient ID
	 * @param integer|null $expires Optional. Expiration time, in UNIX time, for token. By default, tokens last forever.
	 *
	 * @return string
	 */
	public function encode($formId, $uniqueId, $expires = null);

	/**
	 * Encoded token to decode
	 *
	 * @since 1.9.0
	 *
	 * @param string $encoded Encoded token
	 *
	 * @return bool|object
	 */
	public function decode($encoded);

	/**
	 * Decode and check that formId and uniqueId match
	 *
	 * @since 1.9.0
	 *
	 * @param string $encoded Encoded token
	 * @param string $formId Form ID to use with
	 * @param string $sessionPublicKey Unique session ID - for use with CF1 should be transient ID
	 *
	 * @return \stdClass|false
	 */
	public function decodeAndCheckPublic( $encoded, $formId, $sessionPublicKey );
}
