<?php


namespace calderawp\calderaforms\cf2\Fields\FieldTypes;


use calderawp\calderaforms\cf2\Fields\FieldType;

class TextFieldType extends FieldType
{

    /** @inheritdoc */
    public static function getType()
    {
        return 'text';
    }
    /** @inheritdoc */
    public static function getCf1Identifier()
    {
        return 'cf2_text';
    }
}