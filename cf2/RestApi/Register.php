<?php


namespace calderawp\calderaforms\cf2\RestApi;


use calderawp\calderaforms\cf2\RestApi\File\CreateFile;
use calderawp\calderaforms\cf2\RestApi\Queue\RunQueue;

class Register implements CalderaRestApiContract
{

    /**
     * Namespace for API routes being managed
     *
     * @since 1.8.0
     *
     * @var string
     */
    private $namespace;

    /**
     * Register constructor.
     *
     *
     * @since 1.8.0
     *
     *
     * @param string $namespace Namespace for API being managed
     */
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    /** @inheritdoc */
    public function getNamespace()
    {
       return $this->namespace;
    }
    /** @inheritdoc */
    public function initEndpoints()
    {
         (new CreateFile() )->add_routes($this->getNamespace());
         (new RunQueue() )->add_routes($this->getNamespace());
         return $this;
    }

}