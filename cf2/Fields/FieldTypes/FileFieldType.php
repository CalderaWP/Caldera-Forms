<?php


namespace calderawp\calderaforms\cf2\Fields\FieldTypes;


use calderawp\calderaforms\cf2\Fields\FieldType;

class FileFieldType extends FieldType
{

    /** @inheritdoc */
    public static function getType()
    {
        return 'file';
    }
    /** @inheritdoc */
    public static function getCf1Identifier()
    {
        return 'cf2_file';
    }
}