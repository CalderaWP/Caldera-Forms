<?php


namespace calderawp\calderaforms\cf2;


use calderawp\calderaforms\cf2\Fields\FieldTypeFactory;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;

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
	 * Get field type factory
	 *
	 * @since 1.8.0
	 *
	 * @return FieldTypeFactory
	 */
	public function getFieldTypeFactory();
}
