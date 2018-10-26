<?php


namespace calderawp\calderaforms\cf2;


use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;

class CalderaFormsV2 extends \calderawp\CalderaContainers\Service\Container implements CalderaFormsV2Contract
{


    public function __construct()
    {
        $this->singleton(Hooks::class, function(){
            return new Hooks($this);
        });
        $this->singleton(Cf1TransientsApi::class, function(){
            return new Cf1TransientsApi();
        });
    }

    public function getHooks(){
        return $this->make(Hooks::class);
    }

    public function getTransientsApi()
    {
       return $this->make(Cf1TransientsApi::class );
    }

}