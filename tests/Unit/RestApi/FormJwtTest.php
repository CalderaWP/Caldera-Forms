<?php

namespace calderawp\calderaforms\Tests\Unit\RestApi;

use calderawp\calderaforms\cf2\RestApi\Token\FormJwt;
use calderawp\calderaforms\Tests\Unit\TestCase;

class FormJwtTest extends TestCase
{

	/**
 *
 * @since 1.9.0
 *
 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::decode()
 */
	public function testDecode()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$jwt = new FormJwt( $secret, $siteUrl);
		$formId = 'cf11';
		$unique = uniqid('cf');
		$token =  $jwt->encode( $formId, $unique );
		$decoded = $jwt->decode($token);
		$this->assertNotEmpty($decoded);

	}

	/**
	 * Make sure that a token generated with a different secret key can not decoded
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::decode()
	 */
	public function testDecodeDifferentKey()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$jwt = new FormJwt( $secret, $siteUrl);
		$jwt2 = new FormJwt( 'HIROY', $siteUrl);
		$formId = 'cf11';
		$unique = uniqid('cf');
		$token =  $jwt->encode( $formId, $unique );
		$decoded = $jwt2->decode($token);
		$this->assertFalse($decoded);

	}

	/**
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::encode()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::decode()
	 */
	public function testCfData()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$jwt = new FormJwt( $secret, $siteUrl);
		$formId = 'cf11';
		$unique = uniqid('cf');
		$token =  $jwt->encode( $formId, $unique );
		$decoded = $jwt->decode($token);
		$this->assertNotEmpty($decoded);
		$cfData = $decoded->cf;
		$this->assertAttributeEquals(
			$formId,
			'fI',
			$cfData
		);
		$this->assertAttributeEquals(
			$unique,
			'sI',
			$cfData
		);
	}

	/**
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::encode()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::decode()
	 */
	public function testIss()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$jwt = new FormJwt( $secret, $siteUrl);
		$formId = 'cf11';
		$unique = uniqid('cf');
		$token =  $jwt->encode( $formId, $unique );
		$decoded = $jwt->decode($token);
		$this->assertNotEmpty($decoded);
		$this->assertAttributeEquals(
			$siteUrl,
			'iss',
			$decoded
		);
	}

	/**
	 * Test we can set expiration
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::encode()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::decode()
	 */
	public function testSetExp()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$exp = time() + 10000000;
		$jwt = new FormJwt( $secret, $siteUrl);
		$formId = 'cf11';
		$unique = uniqid('cf');
		$token =  $jwt->encode( $formId, $unique,$exp );
		$decoded = $jwt->decode($token);
		$this->assertAttributeEquals(
			$exp,
			'exp',
			$decoded
		);
	}

	/**
	 * Test we can not decode expired
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::encode()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::decode()
	 */
	public function testExpired()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$exp = time() - 5;
		$jwt = new FormJwt( $secret, $siteUrl);
		$formId = 'cf11';
		$unique = uniqid('cf');
		$token =  $jwt->encode( $formId, $unique,$exp );
		$decoded = $jwt->decode($token);
		$this->assertFalse($decoded);
	}



	/**
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::encode()
	 */
	public function testEncode()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$jwt = new FormJwt( $secret, $siteUrl);
		$formId = 'cf11';
		$unique = uniqid('cf');
		$this->assertTrue( is_string($jwt->encode($formId,$unique)));
		$this->assertNotEmpty($jwt->encode($formId,$unique));

	}

	/**
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::construct()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::$secret
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::$siteUrl
	 */
	public function test__construct()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$jwt = new FormJwt( $secret, $siteUrl);

		$this->assertAttributeEquals(

			$secret,
			'secret',
			$jwt

		);
		$this->assertAttributeEquals(
			$siteUrl,
			'siteUrl',
			$jwt

		);
	}

	/**
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::getSecret()
	 */
	public function testGetSecret()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$jwt = new FormJwt( $secret, $siteUrl);
		$this->assertEquals($secret, $jwt->getSecret() );


	}

	/**
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Token\FormJwt::getSiteUrl()
	 */
	public function testGetSiteUrl()
	{
		$siteUrl = 'https://calderaForms.com';
		$secret = 'secret-sauce';
		$jwt = new FormJwt( $secret, $siteUrl);
		$this->assertEquals($siteUrl, $jwt->getSiteUrl() );
	}
}
