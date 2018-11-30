<?php


namespace calderawp\calderaforms\cf2;


use calderawp\calderaforms\cf2\Fields\FieldTypeFactory;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\cf2\Services\ServiceContract;

interface CalderaFormsV2Contract
{

	/**
	 * Get WordPress Transients API wrapper
	 *
	 * @since 1.8.0
	 *
	 * @return Cf1TransientsApi
	 */
	public function getTransientsApi();

	/**
	 * Get WordPress Plugins API manager
	 *
	 * @since 1.8.0
	 *
	 * @return Hooks
	 */
	public function getHooks();


	/**
	 * Set path to main plugin file
	 *
	 * @since 1.8.0
	 *
	 * @param string $coreDirPath
	 *
	 * @return $this
	 */
	public function setCoreDir($coreDirPath);

	/**
	 * Get path to main plugin file
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	public function getCoreDir();


	/**
	 * Set URL to main plugin file
	 *
	 * @since 1.8.0
	 *
	 * @param string $coreUrl
	 *
	 * @return $this
	 */
	public function setCoreUrl($coreUrl);

	/**
	 * Get URL path to main plugin file
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	public function getCoreUrl();

	/**
	 * Get field type factory
	 *
	 * @since 1.8.0
	 *
	 * @return FieldTypeFactory
	 */
	public function getFieldTypeFactory();

	/**
	 * Get global $wpdb
	 *
	 * @since 1.8.0
	 *
	 * @return \wpdb
	 */
	public function getWpdb();

	/**
	 * Register a service with container
	 *
	 * @since 1.8.0
	 *
	 * @param ServiceContract $service The service to register
	 *
	 * @param boolean $isSingleton Is service a singleton?
	 *
	 * @return $this
	 */
	public function registerService( ServiceContract $service, $isSingleton );

	/**
	 * Get service from container
	 *
	 * @since 1.8.0
	 *
	 * @param string $identifier
	 *
	 * @return mixed
	 */
	public function getService($identifier);
}