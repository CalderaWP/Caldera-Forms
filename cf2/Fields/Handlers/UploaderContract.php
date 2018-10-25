<?php


namespace calderawp\calderaforms\cf2\Fields\Handlers;


interface UploaderContract
{

    public function upload($file, array $args = array());

}