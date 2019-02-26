<?php


namespace calderawp\calderaforms\cf2\RestApi;


interface CalderaRestApiContract
{
    /**
     * Get the namespace for Caldera Forms REST API
     *
     * @since 1.8.0
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Initialize the endpoints
     *
     * @since 1.8.0
     *
     * @return $this
     *
     */
    public function initEndpoints();
}