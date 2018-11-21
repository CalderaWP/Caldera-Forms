<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;


interface UploaderContract
{

    /**
     * Do the upload
     *
     * @param array $file File to upload
     * @param array $args Optional Additonal args to pass to upload function
     * @return mixed
     */
    public function upload($file, array $args = array());

    /**
     * Add upload related filters
     *
     * Changes directory name
     *
     * @since 1.8.0
     *
     * @param string $fieldId The field ID for file field
     * @param string $formId The form ID
     * @param boolean $private
     * @return void
     */
    public function addFilter($fieldId, $formId, $private );

    /**
     * Remove upload related filters
     *
     * @since 1.8.0
     *
     * @return void
     */
    public function removeFilter();
}