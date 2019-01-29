<?php


namespace calderawp\calderaforms\cf2\RestApi\File;


use calderawp\calderaforms\cf2\RestApi\Endpoint;

abstract class File extends Endpoint
{

    const URI = 'file';
    /** @inheritdoc */
    protected function getUri()
    {
        return self::URI;
    }
}