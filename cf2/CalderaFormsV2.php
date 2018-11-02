<?php


namespace calderawp\calderaforms\cf2;


use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;

class CalderaFormsV2 extends \calderawp\CalderaContainers\Service\Container implements CalderaFormsV2Contract
{


	/**
	 * CalderaFormsV2 constructor.
	 *
	 * @since 1.8.0
	 */
    public function __construct()
    {
        $this->singleton(Hooks::class, function(){
            return new Hooks($this);
        });
        $this->singleton(Cf1TransientsApi::class, function(){
            return new Cf1TransientsApi();
        });
    }

	/**
	 * Get the singleton hooks instance
	 *
	 * @since 1.8.0
	 *
	 * @return \calderawp\CalderaContainers\Interfaces\ProvidesService|Hooks
	 */
    public function getHooks(){
        return $this->make(Hooks::class);
    }

	/**
	 * Get our transients API
	 *
	 * @since 1.8.0
	 *
	 * @return \calderawp\CalderaContainers\Interfaces\ProvidesService|Cf1TransientsApi
	 */
    public function getTransientsApi()
    {
       return $this->make(Cf1TransientsApi::class );
    }

}
