<?php


namespace calderawp\CalderaContainers\Interfaces;

/**
 * Interface ProvidesService
 *
 * Interface that all service providers MUST implement
 *
 */
interface ProvidesService
{
	/**
	 * Register provider
	 *
	 * @param ServiceContainer $container
	 */
	public function registerService(ServiceContainer $container);
}
