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

}