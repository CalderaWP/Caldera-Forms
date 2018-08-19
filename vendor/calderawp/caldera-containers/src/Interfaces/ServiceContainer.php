<?php


namespace calderawp\CalderaContainers\Interfaces;

/**
 * Interface ServiceContainer
 *
 * Interface that all Service Containers MUST implement
 */
interface ServiceContainer
{

	/**
	 * Check if container provides a service
	 *
	 * @param string $serviceName Service name, should be ::class reference
	 * @return bool
	 */
	public function doesProvide($serviceName);

	/**
	 * Bind a service to the container.
	 *
	 * @param string $alias Alias for class
	 * @param object|\Closure $concrete Concrete class
	co	 */
	public function bind($alias, $concrete);

	/**
	 * Request a service from the container.
	 *
	 * @param string $alias Alias for class
	 * @return object|mixed
	 */
	public function make($alias);

	/**
	 * Bind a singleton instance to the container.
	 *
	 * @param string $alias Alias for class
	 * @param object $binding Single instance of object to bind
	 */
	public function singleton($alias, $binding);
}
