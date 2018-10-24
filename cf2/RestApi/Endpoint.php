<?php


namespace calderawp\calderaforms\cf2\RestApi;


abstract class Endpoint implements \Caldera_Forms_API_Route
{

    /**
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
     * @param string$formId
     * @return $this
     */
    protected function setFormById( $formId )
    {
        $this->form = \Caldera_Forms_Forms::get_form($formId );
        return $this;
    }

    /**
     * @return array
     */
    protected function getForm()
    {
        return $this->form;
    }

}