<?php


namespace calderawp\calderaforms\cf2\RestApi;


abstract class Endpoint implements \Caldera_Forms_API_Route
{

    /**
     * Form config
     *
     * @since 1.8.0
     *
     * @var array
     */
    private $form;

    // @phpcs:disable
    final public function add_routes($namespace)
    // @phpcs:enable
    {
        register_rest_route( $namespace, $this->getUri(), $this->getArgs() );
    }

    /**
     * Get route URI
     *
     * @since 1.8.0
     *
     * @return string
     */
    abstract protected function getUri();

    /**
     * Get route arguments
     *
     * @since 1.8.0
     *
     * @return string
     */
    abstract protected function getArgs();


    /**
     * Set $this->form by looking form up in db
     *
     * @param string $formId Form ID to find
     * @return $this
     */
    protected function setFormById( $formId )
    {
        $this->form = \Caldera_Forms_Forms::get_form($formId );
        return $this;
    }

    /**
     * Get form config
     *
     * @since 1.8.0
     *
     * @return array
     */
    protected function getForm()
    {
        return $this->form;
    }

}