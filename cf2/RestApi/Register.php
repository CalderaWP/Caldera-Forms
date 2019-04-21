<?php


namespace calderawp\calderaforms\cf2\RestApi;


use calderawp\calderaforms\cf2\RestApi\File\CreateFile;
use calderawp\calderaforms\cf2\RestApi\Process\CreateToken;
use calderawp\calderaforms\cf2\RestApi\Process\Submission;
use calderawp\calderaforms\cf2\RestApi\Queue\RunQueue;
use calderawp\calderaforms\cf2\RestApi\Token\ContainsFormJwt;
use calderawp\calderaforms\cf2\RestApi\Token\FormTokenContract;
use calderawp\calderaforms\cf2\RestApi\Token\UsesFormJwtContract;

class Register implements CalderaRestApiContract, UsesFormJwtContract
{

	use ContainsFormJwt;

	/**
	 * Namespace for API routes being managed
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	private $namespace;

	/**
	 * @var 1.9.0
	 */
	private $endpoints;

	/**
	 * Register constructor.
	 *
	 *
	 * @since 1.8.0
	 *
	 *
	 * @param string $namespace Namespace for API being managed
	 */
	public function __construct($namespace)
	{
		$this->namespace = $namespace;
	}

	/** @inheritdoc */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 *
	 *
	 * @return array
	 */
	private function cf2EndpointArgs()
	{
		return [
			CreateFile::class => [
				'jwt' => false,
			],
			RunQueue::class => [
				'jwt' => false,
			],
			Submission::class => [
				'jwt' => 'form',
			],
			CreateToken::class => [
				'jwt' => 'form',
			],

		];
	}

	/** @inheritdoc */
	public function initEndpoints()
	{
		foreach ($this->cf2EndpointArgs() as $endpoint => $args ){
			$this->endpoints[ $endpoint ] = new $endpoint();
			$this->endpoints[ $endpoint ]->add_routes($this->getNamespace());
		}
		return $this;
	}

	/**
	 * Get the collection of endpoints
	 *
	 * @since 1.9.0
	 *
	 * @return array
	 */
	public function getEndpoints()
	{
		return $this->endpoints;
	}

	/** @inheritdoc */
	public function setJwt(FormTokenContract $jwt)
	{
		$this->jwt = $jwt;
		foreach ($this->cf2EndpointArgs() as $endpoint => $args ){
			if( 'form' === $args['jwt']){
				$this->endpoints[ $endpoint ]->setJwt($this->getJwt($jwt));

			}
		}
		return $this;



	}


}
