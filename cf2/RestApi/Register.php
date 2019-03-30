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

	/** @inheritdoc */
	public function initEndpoints()
	{
		$this->endpoints[ CreateFile::class ] = new CreateFile();
		$this->endpoints[ CreateFile::class ]->add_routes($this->getNamespace());

		$this->endpoints[ RunQueue::class ] = new RunQueue();
		$this->endpoints[ RunQueue::class ]->add_routes($this->getNamespace());

		$this->endpoints[ Submission::class ] = new Submission();
		$this->endpoints[ Submission::class ]->add_routes($this->getNamespace());

		$this->endpoints[ CreateToken::class ] = new CreateToken();
		$this->endpoints[ CreateToken::class ]->add_routes($this->getNamespace());


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
		foreach (
			[CreateToken::class,Submission::class] as $endpoint
		){
			$this->endpoints[ $endpoint ]->setJwt($this->getJwt($jwt));

		}
		return $this;

	}


}
