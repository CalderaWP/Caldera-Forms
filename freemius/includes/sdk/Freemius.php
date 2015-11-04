<?php
	/**
	 * Copyright 2014 Freemius, Inc.
	 *
	 * Licensed under the GPL v2 (the "License"); you may
	 * not use this file except in compliance with the License. You may obtain
	 * a copy of the License at
	 *
	 *     http://choosealicense.com/licenses/gpl-v2/
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
	 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
	 * License for the specific language governing permissions and limitations
	 * under the License.
	 */

	require_once( dirname( __FILE__ ) . '/FreemiusBase.php' );

	define( 'FS_SDK__USER_AGENT', 'fs-php-' . Freemius_Api_Base::VERSION );

	if ( ! defined( 'FS_SDK__SIMULATE_NO_CURL' ) ) {
		define( 'FS_SDK__SIMULATE_NO_CURL', false );
	}

	if ( ! defined( 'FS_SDK__SIMULATE_NO_API_CONNECTIVITY_CLOUDFLARE' ) ) {
		define( 'FS_SDK__SIMULATE_NO_API_CONNECTIVITY_CLOUDFLARE', false );
	}

	if ( ! defined( 'FS_SDK__SIMULATE_NO_API_CONNECTIVITY_SQUID_ACL' ) ) {
		define( 'FS_SDK__SIMULATE_NO_API_CONNECTIVITY_SQUID_ACL', false );
	}

	define( 'FS_SDK__HAS_CURL', ! FS_SDK__SIMULATE_NO_CURL && function_exists( 'curl_version' ) );

	if ( ! FS_SDK__HAS_CURL ) {
		$curl_version = array( 'version' => '7.0.0' );
	} else {
		$curl_version = curl_version();
	}

	define( 'FS_API__PROTOCOL', version_compare( $curl_version['version'], '7.37', '>=' ) ? 'https' : 'http' );

	if ( ! defined( 'FS_API__ADDRESS' ) ) {
		define( 'FS_API__ADDRESS', '://api.freemius.com' );
	}
	if ( ! defined( 'FS_API__SANDBOX_ADDRESS' ) ) {
		define( 'FS_API__SANDBOX_ADDRESS', '://sandbox-api.freemius.com' );
	}

	class Freemius_Api extends Freemius_Api_Base {
		/**
		 * Default options for curl.
		 */
		public static $CURL_OPTS = array(
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_USERAGENT      => FS_SDK__USER_AGENT,
		);

		/**
		 * @param string      $pScope   'app', 'developer', 'user' or 'install'.
		 * @param number      $pID      Element's id.
		 * @param string      $pPublic  Public key.
		 * @param string|bool $pSecret  Element's secret key.
		 * @param bool        $pSandbox Whether or not to run API in sandbox mode.
		 */
		public function __construct( $pScope, $pID, $pPublic, $pSecret = false, $pSandbox = false ) {
			// If secret key not provided, use public key encryption.
			if ( is_bool( $pSecret ) ) {
				$pSecret = $pPublic;
			}

			parent::Init( $pScope, $pID, $pPublic, $pSecret, $pSandbox );
		}

		public function GetUrl( $pCanonizedPath = '' ) {
			$address = ( $this->_sandbox ? FS_API__SANDBOX_ADDRESS : FS_API__ADDRESS );

			if ( ':' === $address[0] ) {
				$address = self::$_protocol . $address;
			}

			return $address . $pCanonizedPath;
		}

		/**
		 * @var int Clock diff in seconds between current server to API server.
		 */
		private static $_clock_diff = 0;

		/**
		 * Set clock diff for all API calls.
		 *
		 * @since 1.0.3
		 *
		 * @param $pSeconds
		 */
		public static function SetClockDiff( $pSeconds ) {
			self::$_clock_diff = $pSeconds;
		}

		/**
		 * @var string http or https
		 */
		private static $_protocol = FS_API__PROTOCOL;

		/**
		 * Set API connection protocol.
		 *
		 * @since 1.0.4
		 */
		public static function SetHttp() {
			self::$_protocol = 'http';
		}

		/**
		 * @since 1.0.4
		 *
		 * @return bool
		 */
		public static function IsHttps() {
			return ( 'https' === self::$_protocol );
		}

		/**
		 * Sign request with the following HTTP headers:
		 *      Content-MD5: MD5(HTTP Request body)
		 *      Date: Current date (i.e Sat, 14 Feb 2015 20:24:46 +0000)
		 *      Authorization: FS {scope_entity_id}:{scope_entity_public_key}:base64encode(sha256(string_to_sign,
		 *      {scope_entity_secret_key}))
		 *
		 * @param string $pResourceUrl
		 * @param array  $opts
		 */
		protected function SignRequest( $pResourceUrl, &$opts ) {
			$eol          = "\n";
			$content_md5  = '';
			$now          = ( time() - self::$_clock_diff );
			$date         = date( 'r', $now );
			$content_type = '';

			if ( isset( $opts[ CURLOPT_POST ] ) && 0 < $opts[ CURLOPT_POST ] ) {
				$content_md5                  = md5( $opts[ CURLOPT_POSTFIELDS ] );
				$opts[ CURLOPT_HTTPHEADER ][] = 'Content-MD5: ' . $content_md5;
				$content_type                 = 'application/json';
			}

			$opts[ CURLOPT_HTTPHEADER ][] = 'Date: ' . $date;

			$string_to_sign = implode( $eol, array(
				$opts[ CURLOPT_CUSTOMREQUEST ],
				$content_md5,
				$content_type,
				$date,
				$pResourceUrl
			) );

			// If secret and public keys are identical, it means that
			// the signature uses public key hash encoding.
			$auth_type = ( $this->_secret !== $this->_public ) ? 'FS' : 'FSP';

			// Add authorization header.
			$opts[ CURLOPT_HTTPHEADER ][] = 'Authorization: ' .
			                                $auth_type . ' ' .
			                                $this->_id . ':' .
			                                $this->_public . ':' .
			                                self::Base64UrlEncode(
				                                hash_hmac( 'sha256', $string_to_sign, $this->_secret )
			                                );
		}

		/**
		 * Get API request URL signed via query string.
		 *
		 * @param string $pPath
		 *
		 * @throws Freemius_Exception
		 *
		 * @return string
		 */
		function GetSignedUrl( $pPath ) {
			$resource     = explode( '?', $this->CanonizePath( $pPath ) );
			$pResourceUrl = $resource[0];

			$eol          = "\n";
			$content_md5  = '';
			$content_type = '';
			$now          = ( time() - self::$_clock_diff );
			$date         = date( 'r', $now );

			$string_to_sign = implode( $eol, array(
				'GET',
				$content_md5,
				$content_type,
				$date,
				$pResourceUrl
			) );

			// If secret and public keys are identical, it means that
			// the signature uses public key hash encoding.
			$auth_type = ( $this->_secret !== $this->_public ) ? 'FS' : 'FSP';

			return $this->GetUrl(
				$pResourceUrl . '?' .
				( 1 < count( $resource ) && ! empty( $resource[1] ) ? $resource[1] . '&' : '' ) .
				http_build_query( array(
					'auth_date'     => $date,
					'authorization' => $auth_type . ' ' . $this->_id . ':' .
					                   $this->_public . ':' .
					                   self::Base64UrlEncode( hash_hmac(
						                   'sha256', $string_to_sign, $this->_secret
					                   ) )
				) ) );
		}

		/**
		 * Makes an HTTP request. This method can be overridden by subclasses if
		 * developers want to do fancier things or use something other than curl to
		 * make the request.
		 *
		 * @param string        $pCanonizedPath The URL to make the request to
		 * @param string        $pMethod        HTTP method
		 * @param array         $params         The parameters to use for the POST body
		 * @param null|resource $ch             Initialized curl handle
		 *
		 * @return object[]|object|null
		 *
		 * @throws Freemius_Exception
		 */
		public function MakeRequest( $pCanonizedPath, $pMethod = 'GET', $params = array(), $ch = null ) {
			if ( !FS_SDK__HAS_CURL ) {
				$this->ThrowNoCurlException();
			}

			// Connectivity errors simulation.
			if ( FS_SDK__SIMULATE_NO_API_CONNECTIVITY_CLOUDFLARE ) {
				$this->ThrowCloudFlareDDoSException();
			} else if ( FS_SDK__SIMULATE_NO_API_CONNECTIVITY_SQUID_ACL ) {
				$this->ThrowSquidAclException();
			}

			if ( ! $ch ) {
				$ch = curl_init();
			}

			$opts = self::$CURL_OPTS;

			if ( ! isset( $opts[ CURLOPT_HTTPHEADER ] ) || ! is_array( $opts[ CURLOPT_HTTPHEADER ] ) ) {
				$opts[ CURLOPT_HTTPHEADER ] = array();
			}

			if ( 'POST' === $pMethod || 'PUT' === $pMethod ) {
				if ( is_array( $params ) && 0 < count( $params ) ) {
					$opts[ CURLOPT_HTTPHEADER ][] = 'Content-Type: application/json';
					$opts[ CURLOPT_POST ]         = count( $params );
					$opts[ CURLOPT_POSTFIELDS ]   = json_encode( $params );
				}

				$opts[ CURLOPT_RETURNTRANSFER ] = true;
			}

			$opts[ CURLOPT_URL ]           = $this->GetUrl( $pCanonizedPath );
			$opts[ CURLOPT_CUSTOMREQUEST ] = $pMethod;

			$resource = explode( '?', $pCanonizedPath );

			// Only sign request if not ping.json connectivity test.
			if ( '/v1/ping.json' !== strtolower( substr( $resource[0], - strlen( '/v1/ping.json' ) ) ) ) {
				$this->SignRequest( $resource[0], $opts );
			}

			// disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
			// for 2 seconds if the server does not support this header.
			$opts[ CURLOPT_HTTPHEADER ][] = 'Expect:';

			if ( 'https' === substr( strtolower( $pCanonizedPath ), 0, 5 ) ) {
				$opts[ CURLOPT_SSL_VERIFYHOST ] = false;
				$opts[ CURLOPT_SSL_VERIFYPEER ] = false;
			}

			curl_setopt_array( $ch, $opts );
			$result = curl_exec( $ch );

			/*if (curl_errno($ch) == 60) // CURLE_SSL_CACERT
			{
				self::errorLog('Invalid or no certificate authority found, using bundled information');
				curl_setopt($ch, CURLOPT_CAINFO,
				dirname(__FILE__) . '/fb_ca_chain_bundle.crt');
				$result = curl_exec($ch);
			}*/

			// With dual stacked DNS responses, it's possible for a server to
			// have IPv6 enabled but not have IPv6 connectivity.  If this is
			// the case, curl will try IPv4 first and if that fails, then it will
			// fall back to IPv6 and the error EHOSTUNREACH is returned by the
			// operating system.
			if ( false === $result && empty( $opts[ CURLOPT_IPRESOLVE ] ) ) {
				$matches = array();
				$regex   = '/Failed to connect to ([^:].*): Network is unreachable/';
				if ( preg_match( $regex, curl_error( $ch ), $matches ) ) {
					if ( strlen( @inet_pton( $matches[1] ) ) === 16 ) {
//						self::errorLog('Invalid IPv6 configuration on server, Please disable or get native IPv6 on your server.');
						self::$CURL_OPTS[ CURLOPT_IPRESOLVE ] = CURL_IPRESOLVE_V4;
						curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
						$result = curl_exec( $ch );
					}
				}
			}

			if ( $result === false ) {
				$e = new Freemius_Exception( array(
					'error' => array(
						'code'    => curl_errno( $ch ),
						'message' => curl_error( $ch ),
						'type'    => 'CurlException',
					),
				) );

				curl_close( $ch );
				throw $e;
			}

			curl_close( $ch );

			if (empty($result))
				return null;

			$decoded = json_decode( $result );

			if ( is_null( $decoded ) ) {
				if ( preg_match( '/Please turn JavaScript on/i', $result ) &&
				     preg_match( '/text\/javascript/', $result )
				) {
					$this->ThrowCloudFlareDDoSException( $result );
				} else if ( preg_match( '/Access control configuration prevents your request from being allowed at this time. Please contact your service provider if you feel this is incorrect./', $result ) &&
				            preg_match( '/squid/', $result )
				) {
					$this->ThrowSquidAclException( $result );
				} else {
					$decoded = (object) array(
						'error' => (object) array(
							'type'    => 'Unknown',
							'message' => $result,
							'code'    => 'unknown',
							'http'    => 402
						)
					);
				}
			}

			return $decoded;
		}

		/**
		 * @param string $pResult
		 *
		 * @throws Freemius_Exception
		 */
		private function ThrowNoCurlException( $pResult = '' ) {
			throw new Freemius_Exception( array(
				'error' => (object) array(
					'type'    => 'cUrlMissing',
					'message' => $pResult,
					'code'    => 'curl_missing',
					'http'    => 402
				)
			) );
		}

		/**
		 * @param string $pResult
		 *
		 * @throws Freemius_Exception
		 */
		private function ThrowCloudFlareDDoSException( $pResult = '' ) {
			throw new Freemius_Exception( array(
				'error' => (object) array(
					'type'    => 'CloudFlareDDoSProtection',
					'message' => $pResult,
					'code'    => 'cloudflare_ddos_protection',
					'http'    => 402
				)
			) );
		}

		/**
		 * @param string $pResult
		 *
		 * @throws Freemius_Exception
		 */
		private function ThrowSquidAclException( $pResult = '' ) {
			throw new Freemius_Exception( array(
				'error' => (object) array(
					'type'    => 'SquidCacheBlock',
					'message' => $pResult,
					'code'    => 'squid_cache_block',
					'http'    => 402
				)
			) );
		}
	}