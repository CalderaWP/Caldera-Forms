<?php


namespace calderawp\calderaforms\cf2;


use calderawp\calderaforms\cf2\Fields\FieldTypeFactory;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\cf2\Services\ServiceContract;
class CalderaFormsV2 extends \calderawp\CalderaContainers\Service\Container implements CalderaFormsV2Contract
{

	/**
	 * Path to main plugin file
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	protected $coreDirPath;


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
		$this->singleton(FieldTypeFactory::class, function(){
			return new FieldTypeFactory();
		});
    }

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
    public function registerService( ServiceContract $service, $isSingleton ){
		if (! $service->isSingleton()) {
			$this->bind($service->getIdentifier(),  $service->register($this) );
		}else{
			$this->singleton($service->getIdentifier(), $service->register($this) );
		}
		return $this;
	}


	/**
	 * Get service from container
	 *
	 * @since 1.8.0
	 *
	 * @param string $identifier
	 *
	 * @return mixed
	 */
	public function getService($identifier){
    	return $this->make($identifier);
	}

	/**
	 * Set path to main plugin file
	 *
	 * @since 1.8.0
	 *
	 * @param string $coreDirPath
	 *
	 * @return $this
	 */
    public function setCoreDir($coreDirPath)
	{
		$this->coreDirPath  = $coreDirPath;
		return $this;
	}

	/**
	 * Get path to main plugin file
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	public function getCoreDir(){
    	if( is_string( $this->coreDirPath ) ){
    		return $this->coreDirPath;
		}
		if( defined( 'CFCORE_PATH' ) ){
			return CFCORE_PATH;
		}

		return '';
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

	/**
	 * Get field type factory
	 *
	 * @since 1.8.0
	 *
	 * @return FieldTypeFactory
	 */
	public function getFieldTypeFactory()
	{
		return $this->make(FieldTypeFactory::class );
	}

	/** @inheritdoc */
	public function getWpdb()
	{
		global $wpdb;
		return $wpdb;
	}
}
