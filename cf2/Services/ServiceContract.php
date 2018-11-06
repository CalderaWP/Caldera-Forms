<?php


namespace calderawp\calderaforms\cf2\Services;


use calderawp\calderaforms\cf2\CalderaFormsV2Contract;

interface ServiceContract
{

	/**
	 * Register a service for container
	 *
	 * Return instance of class container should provide.
	 *
	 * @since  1.8.0
	 *
	 * @param CalderaFormsV2Contract $container
	 *
	 * @return CalderaFormsV2Contract
	 */
	public function register(CalderaFormsV2Contract $container );

	/**
	 * Get identifier for service
	 *
	 * @since  1.8.0
	 *
	 * @return string
	 */
	public function getIdentifier();

	/**
	 * Is service a singleton or not?
	 *
	 * @since  1.8.0
	 *
	 * @return bool
	 */
	public function isSingleton();

}