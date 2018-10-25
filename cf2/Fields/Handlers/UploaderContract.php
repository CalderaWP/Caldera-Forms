<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;


interface UploaderContract
{

    public function upload($file, array $args = array());

    /**
     * @since 1.8.0
     *
     * @param string $fieldId The field ID for file field
     * @param string $formId The form ID
     * @param boolean $private
     * @return void
     */
    public function addFilter($fieldId, $formId, $private );
    /**
     * @since 1.8.0
     * @return void
     */
    public function removeFilter();
}