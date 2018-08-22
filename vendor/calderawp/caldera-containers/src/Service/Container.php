<?php


namespace calderawp\CalderaContainers\Service;

use calderawp\CalderaContainers\Interfaces\ProvidesService;
use calderawp\CalderaContainers\Interfaces\ServiceContainer;

/**
 * Class Container
 *
 * Primary service container.
 *
 * This is based on WP Pusher's service container.
 * Cool plugin for managin plugin/ theme installs with git.
 * -> https://wppusher.com/
 */
class Container implements ServiceContainer
{

	/**
	 * @var ProvidesService[]
	 */
	protected $services;

	/** @inheritdoc */
	public function doesProvide($serviceName)
	{
		if (! is_array($this->services)) {
			$this->services = [];
		}

		return ! empty($this->services) && array_key_exists($serviceName, $this->services);
	}

	/** @inheritdoc */
	public function bind($alias, $concrete)
	{
		$this->services[$alias] = $concrete;
	}

	/** @inheritdoc */
	public function make($alias)
	{
		if (! isset($this->services[$alias])) {
			return $this->resolve($alias);
		}
		if (is_callable($this->services[$alias])) {
			return call_user_func_array($this->services[$alias], array($this));
		}

		if (is_object($this->services[$alias])) {
			return $this->services[$alias];
		}

		if (class_exists($this->services[$alias])) {
			return $this->resolve($this->services[$alias]);
		}

		return $this->resolve($alias);
	}

	/** @inheritdoc */
	public function singleton($alias, $binding)
	{
		$this->services[$alias] = $binding;
	}


	/**
	 * Resolve dependencies.
	 *
	 * @todo use Doctrine Insanitator?
	 *
	 * @param $class
	 * @return object
	 */
	private function resolve($class)
	{
		$reflection = new \ReflectionClass($class);

		$constructor = $reflection->getConstructor();

		// Constructor is null
		if (! $constructor) {
			return new $class;
		}

		// Constructor with no parameters
		$params = $constructor->getParameters();

		if (count($params) === 0) {
			return new $class;
		}

		$newInstanceParams = array();

		foreach ($params as $param) {
			if (is_null($param->getClass())) {
				$newInstanceParams[] = null;
				continue;
			}

			$newInstanceParams[] = $this->make(
				$param->getClass()->getName()
			);
		}

		return $reflection->newInstanceArgs(
			$newInstanceParams
		);
	}
}
