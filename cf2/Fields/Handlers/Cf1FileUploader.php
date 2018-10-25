<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;
use calderawp\calderaforms\cf2\Fields\Handlers\UploaderContract;


class Cf1FileUploader implements UploaderContract
{

    /** @inheritdoc */
    public function upload($file, array $args = array())
    {
       return \Caldera_Forms_Files::upload($file,$args);
    }

    public function addFilter($fieldId, $formId, $private)
    {
        \Caldera_Forms_Files::add_upload_filter($fieldId,$formId,$private);
    }

    public function removeFilter()
    {
       \Caldera_Forms_Files::remove_upload_filter();
    }
}