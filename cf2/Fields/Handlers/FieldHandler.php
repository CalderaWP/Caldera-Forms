<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;


use calderawp\calderaforms\cf2\CalderaFormsV2Contract;

abstract class FieldHandler
{
    /**
     * @var CalderaFormsV2Contract
     */
    protected $container;


    public function __construct(CalderaFormsV2Contract $container)
    {
        $this->container = $container;
    }
}