<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;


use calderawp\calderaforms\cf2\CalderaFormsV2Contract;

abstract class FieldHandler implements FieldHandlerContract
{
    /**
     * CF2 Container
     *
     * @since 1.8.0
     *
     * @var CalderaFormsV2Contract
     */
    protected $container;


    /**
     * FieldHandler constructor.
     *
     * @since 1.8.0
     *
     * @param CalderaFormsV2Contract $container
     */
    public function __construct(CalderaFormsV2Contract $container)
    {
        $this->container = $container;
    }
}