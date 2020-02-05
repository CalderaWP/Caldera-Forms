<?php


namespace calderawp\calderaforms\cf2\Services;


use calderawp\calderaforms\cf2\CalderaFormsV2Contract;
use calderawp\calderaforms\cf2\Factories\Processor;

class ProcessorService extends Service
{


    /** @inheritDoc */
    public function isSingleton()
    {
       return true;
    }

    /** @inheritDoc */
    public function register(CalderaFormsV2Contract $container)
    {
        return new Processor();
    }
}